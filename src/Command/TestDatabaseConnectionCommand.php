<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestDatabaseConnectionCommand extends Command
{
    protected static $defaultName = 'app:test-database-connection';
    protected static $defaultDescription = 'Test database connections for module databases';

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Testing Database Connections');

        $baseUrl = $this->params->get('DATABASE_URL');
        $io->text("Base URL: $baseUrl");

        // Test common module databases
        $moduleIds = ['186', '187', '193', '194', '195', '101'];
        
        foreach ($moduleIds as $moduleId) {
            $dbName = "Module_{$moduleId}";
            $io->text("Testing $dbName...");
            
            try {
                // Parse base URL and replace database name
                $urlParts = parse_url($baseUrl);
                $urlParts['path'] = '/' . $dbName;
                
                $moduleUrl = $this->buildUrl($urlParts);
                $io->text("Module URL: $moduleUrl");
                
                $connection = DriverManager::getConnection(['url' => $moduleUrl]);
                
                // Try to query tables
                $stmt = $connection->prepare("SHOW TABLES");
                $stmt->executeQuery();
                $tables = $stmt->fetchAllAssociative();
                
                $io->text("✓ $dbName exists with " . count($tables) . " tables");
                
                // Check for GWAS tables
                $expectedTables = ['ind', 'pos', 'chr', 'pval', 'ratio'];
                $foundTables = [];
                foreach ($tables as $table) {
                    $foundTables[] = $table['Tables_in_' . strtolower($dbName)] ?? $table[0] ?? '';
                }
                $hasRequiredTables = count(array_intersect($expectedTables, $foundTables)) >= 3;
                
                if ($hasRequiredTables) {
                    $io->text("✓ $dbName has required GWAS tables");
                    
                    // Check for data
                    $dataStmt = $connection->prepare("SELECT COUNT(*) FROM ind LIMIT 1");
                    $dataStmt->executeQuery();
                    $count = $dataStmt->fetchOne();
                    $io->text("✓ $dbName has $count records in ind table");
                } else {
                    $io->text("✗ $dbName missing required GWAS tables");
                }
                
                $connection->close();
                
            } catch (\Exception $e) {
                $io->text("✗ $dbName connection failed: " . $e->getMessage());
            }
            
            $io->newLine();
        }

        return Command::SUCCESS;
    }

    private function buildUrl(array $parts): string
    {
        $url = '';
        
        if (isset($parts['scheme'])) {
            $url .= $parts['scheme'] . '://';
        }
        
        if (isset($parts['user'])) {
            $url .= $parts['user'];
            if (isset($parts['pass'])) {
                $url .= ':' . $parts['pass'];
            }
            $url .= '@';
        }
        
        if (isset($parts['host'])) {
            $url .= $parts['host'];
        }
        
        if (isset($parts['port'])) {
            $url .= ':' . $parts['port'];
        }
        
        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $url .= '?' . $parts['query'];
        }
        
        if (isset($parts['fragment'])) {
            $url .= '#' . $parts['fragment'];
        }
        
        return $url;
    }
} 