<?php

namespace App\Command;

use App\Entity\SONGBIRD\Genes;
use App\Repository\GenesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-genes',
    description: 'Import genes data from CSV file into the database',
)]
class ImportGenesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GenesRepository $genesRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $csvFile = 'data/gwatch_db_files/gwatch_genes_export_2025-08-31_23-25-42.csv';
        
        if (!file_exists($csvFile)) {
            $io->error("CSV file not found: $csvFile");
            return Command::FAILURE;
        }
        
        $io->title('Importing Genes Data');
        $io->text("Reading from: $csvFile");
        
        // Clear existing data
        $io->text('Clearing existing genes data...');
        $this->entityManager->createQuery('DELETE FROM App\Entity\Gwatch\Genes')->execute();
        
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $io->error("Could not open CSV file: $csvFile");
            return Command::FAILURE;
        }
        
        // Skip header row
        fgetcsv($handle);
        
        $batchSize = 1000;
        $count = 0;
        $totalRows = 0;
        
        $io->progressStart();
        
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) >= 6) {
                $genes = new Genes();
                $genes->setBuild((int) $data[0]);
                $genes->setChr((int) $data[1]);
                $genes->setPosstart((int) $data[2]);
                $genes->setPosend((int) $data[3]);
                $genes->setStrand((int) $data[4]);
                $genes->setGene($data[5]);
                
                $this->entityManager->persist($genes);
                $count++;
                $totalRows++;
                
                // Flush in batches to avoid memory issues
                if ($count % $batchSize === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    $io->progressAdvance($batchSize);
                }
            }
        }
        
        // Flush remaining entities
        if ($count > 0) {
            $this->entityManager->flush();
            $io->progressAdvance($count % $batchSize);
        }
        
        fclose($handle);
        $io->progressFinish();
        
        $io->success("Successfully imported $totalRows genes records");
        
        return Command::SUCCESS;
    }
}
