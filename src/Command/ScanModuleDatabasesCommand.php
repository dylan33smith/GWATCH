<?php

namespace App\Command;

use App\Service\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScanModuleDatabasesCommand extends Command
{
    protected static $defaultName = 'app:scan-module-databases';
    protected static $defaultDescription = 'Scan for available module databases and register them';

    private $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Scanning for Module Databases');

        // Common module IDs to check
        $moduleIds = ['186', '187', '193', '194', '195', '101'];
        $foundModules = [];
        $registeredModules = [];

        foreach ($moduleIds as $moduleId) {
            if ($this->databaseManager->moduleDatabaseExists($moduleId)) {
                $foundModules[] = $moduleId;
                
                // Register the module
                if ($this->databaseManager->registerModule($moduleId, 1)) {
                    $registeredModules[] = $moduleId;
                    $io->text("✓ Registered Module $moduleId");
                } else {
                    $io->text("⚠ Module $moduleId already registered");
                }
            }
        }

        if (empty($foundModules)) {
            $io->warning('No module databases found');
            return Command::FAILURE;
        }

        $io->success(sprintf(
            'Found %d module databases, registered %d new modules',
            count($foundModules),
            count($registeredModules)
        ));

        $io->table(
            ['Module ID', 'Status'],
            array_map(function($moduleId) use ($registeredModules) {
                return [
                    $moduleId,
                    in_array($moduleId, $registeredModules) ? 'Registered' : 'Already Registered'
                ];
            }, $foundModules)
        );

        return Command::SUCCESS;
    }
} 