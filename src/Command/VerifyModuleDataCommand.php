<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:verify-module-data',
    description: 'Verify that imported module data matches CSV directory row counts'
)]
class VerifyModuleDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('csv-dir', InputArgument::REQUIRED, 'Path to directory containing CSV files (extracted)')
            ->addOption('module-id', null, InputOption::VALUE_REQUIRED, 'Numeric module id (e.g., 186)')
            ->addOption('module-db', null, InputOption::VALUE_REQUIRED, 'Module database name (e.g., Module_186)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvDir = rtrim($input->getArgument('csv-dir'), '/');
        $moduleDb = $input->getOption('module-db');
        $moduleId = $input->getOption('module-id');

        if (!$moduleDb) {
            if (!$moduleId) {
                $io->error('Provide either --module-db or --module-id');
                return Command::FAILURE;
            }
            $moduleDb = 'Module_' . $moduleId;
        }

        if (!is_dir($csvDir)) {
            $io->error('CSV directory not found: ' . $csvDir);
            return Command::FAILURE;
        }

        /** @var Connection $conn */
        $conn = $this->entityManager->getConnection();
        $conn->executeStatement(sprintf('USE `%s`', $moduleDb));

        $failures = [];

        // Helper to count lines quickly
        $countLines = function (string $path, bool $hasHeader = false): int {
            $h = @fopen($path, 'r');
            if (!$h) {
                return -1;
            }
            $count = 0;
            while (!feof($h)) {
                $buf = fgets($h);
                if ($buf !== false) {
                    $count++;
                }
            }
            fclose($h);
            if ($hasHeader && $count > 0) {
                $count -= 1;
            }
            return $count;
        };

        $checks = [
            ['file' => 'chr.csv', 'table' => 'chr', 'header' => false],
            ['file' => 'chrsupp.csv', 'table' => 'chrsupp', 'header' => false],
            ['file' => 'col.csv', 'table' => 'col', 'header' => false],
            ['file' => 'ind.csv', 'table' => 'ind', 'header' => false],
            ['file' => 'r_pval.csv', 'table' => 'r_pval', 'header' => false],
            ['file' => 'r_ratio.csv', 'table' => 'r_ratio', 'header' => false],
            ['file' => 'v_ind.csv', 'table' => 'v_ind', 'header' => false],
            ['file' => 'val.csv', 'table' => 'pval', 'header' => false],
            ['file' => 'val.csv', 'table' => 'ratio', 'header' => false],
            ['file' => 'row.csv', 'table' => 'pos', 'header' => false],
            ['file' => 'row.csv', 'table' => 'alias', 'header' => false],
            ['file' => 'row.csv', 'table' => 'allele', 'header' => false],
            ['file' => 'row.csv', 'table' => 'maf', 'header' => false],
        ];

        foreach ($checks as $check) {
            $filePath = $csvDir . '/' . $check['file'];
            $expected = $countLines($filePath, $check['header']);
            if ($expected < 0) {
                $failures[] = sprintf('Missing file: %s', $filePath);
                continue;
            }
            $actual = (int)$conn->fetchOne('SELECT COUNT(*) FROM `' . $check['table'] . '`');
            if ($actual !== $expected) {
                $failures[] = sprintf('%s mismatch (expected %d from %s, got %d)', $check['table'], $expected, $check['file'], $actual);
            }
        }

        // radius_ind.csv (optional, has header)
        $radiusPath = $csvDir . '/radius_ind.csv';
        if (is_file($radiusPath)) {
            $expected = $countLines($radiusPath, true);
            $actual = (int)$conn->fetchOne('SELECT COUNT(*) FROM `radius_ind`');
            if ($actual !== $expected) {
                $failures[] = sprintf('radius_ind mismatch (expected %d from radius_ind.csv, got %d)', $expected, $actual);
            }
        }

        // density files sum (each has header)
        $densityExpected = 0;
        $dir = opendir($csvDir);
        if ($dir !== false) {
            while (($entry = readdir($dir)) !== false) {
                if (preg_match('/^density_\d+\.csv$/', $entry)) {
                    $densityExpected += max(0, $countLines($csvDir . '/' . $entry, true));
                }
            }
            closedir($dir);
        }

        if ($densityExpected > 0) {
            $actual = (int)$conn->fetchOne('SELECT COUNT(*) FROM `top_hits`');
            if ($actual !== $densityExpected) {
                $failures[] = sprintf('top_hits mismatch (expected %d from density_*.csv, got %d)', $densityExpected, $actual);
            }
        }

        if (empty($failures)) {
            $io->success('All table counts match CSVs for ' . $moduleDb);
            return Command::SUCCESS;
        }

        $io->error('Mismatches found for ' . $moduleDb);
        foreach ($failures as $f) {
            $io->writeln(' - ' . $f);
        }
        return Command::FAILURE;
    }
}

