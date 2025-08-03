<?php

namespace App\Command;

use App\Service\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportModuleDataCommand extends Command
{
    protected static $defaultName = 'app:import-module-data';
    protected static $defaultDescription = 'Import CSV data for a specific module into its own database';

    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('module-id', InputArgument::REQUIRED, 'Module ID (e.g., 186)')
            ->addArgument('data-path', InputArgument::REQUIRED, 'Path to module data directory')
            ->setHelp('This command imports CSV data for a module into a separate database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $moduleId = $input->getArgument('module-id');
        $dataPath = $input->getArgument('data-path');

        $io->title("Importing data for Module $moduleId");

        // Check if data directory exists
        if (!is_dir($dataPath)) {
            $io->error("Data directory not found: $dataPath");
            return Command::FAILURE;
        }

        // Create database and import data
        if ($this->databaseManager->importModuleData($moduleId, $dataPath)) {
            $io->success("Successfully imported data for Module $moduleId");
            return Command::SUCCESS;
        } else {
            $io->error("Failed to import data for Module $moduleId");
            return Command::FAILURE;
        }
    }
} 