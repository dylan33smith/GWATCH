<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddSampleModulesCommand extends Command
{
    protected static $defaultName = 'app:add-sample-modules';
    protected static $defaultDescription = 'Add sample modules to the tracking table';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Adding Sample Modules');

        try {
            $connection = $this->entityManager->getConnection();
            
            // Add sample modules
            $sampleModules = [
                ['module_id' => '186', 'owner_id' => 1, 'visible' => 1],
                ['module_id' => '187', 'owner_id' => 1, 'visible' => 1],
                ['module_id' => '193', 'owner_id' => 1, 'visible' => 1],
                ['module_id' => '194', 'owner_id' => 1, 'visible' => 1],
                ['module_id' => '195', 'owner_id' => 1, 'visible' => 1],
            ];
            
            foreach ($sampleModules as $module) {
                try {
                    $sql = "INSERT INTO module_tracking (module_id, owner_id, visible, created_at) VALUES (?, ?, ?, NOW())";
                    $stmt = $connection->prepare($sql);
                    $stmt->executeStatement([$module['module_id'], $module['owner_id'], $module['visible']]);
                    $io->text("✓ Added Module {$module['module_id']}");
                } catch (\Exception $e) {
                    $io->text("⚠ Module {$module['module_id']} already exists or error: " . $e->getMessage());
                }
            }
            
            $io->success('Sample modules added successfully');
            
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
} 