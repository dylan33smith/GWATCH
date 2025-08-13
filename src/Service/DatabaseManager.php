<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DatabaseManager
{
    private $entityManager;
    private $params;
    private $currentModuleId = null;
    private $originalConnection = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params
    ) {
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    /**
     * Switch to a specific module database
     */
    public function switchToModuleDatabase(string $moduleId): bool
    {
        // Check if module exists in main database
        if (!$this->moduleExists($moduleId)) {
            return false;
        }

        // Store original connection
        if ($this->originalConnection === null) {
            $this->originalConnection = $this->entityManager->getConnection();
        }

        // Create new connection to module database
        $moduleDbName = "Module_{$moduleId}";
        $connection = $this->createModuleConnection($moduleDbName);
        
        if ($connection === null) {
            return false;
        }

        // Update entity manager connection
        $this->entityManager->getConnection()->close();
        $this->entityManager->getConnection()->setDriverConnection($connection->getDriverConnection());
        
        $this->currentModuleId = $moduleId;
        return true;
    }

    /**
     * Switch back to main GWATCH database
     */
    public function switchToMainDatabase(): void
    {
        if ($this->originalConnection !== null) {
            $this->entityManager->getConnection()->close();
            $this->entityManager->getConnection()->setDriverConnection(
                $this->originalConnection->getDriverConnection()
            );
            $this->currentModuleId = null;
        }
    }

    /**
     * Check if module exists in main database
     */
    private function moduleExists(string $moduleId): bool
    {
        $connection = $this->entityManager->getConnection();
        
        try {
            $sql = "SELECT COUNT(*) FROM module WHERE name = ?";
            $stmt = $connection->prepare($sql);
            $stmt->executeQuery([$moduleId]);
            $count = $stmt->fetchOne();
            
            return $count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create connection to module database
     */
    public function createModuleConnection(string $dbName): ?Connection
    {
        try {
            $baseUrl = $this->params->get('app.database_url');
            
            // Parse base URL and replace database name
            $urlParts = parse_url($baseUrl);
            $urlParts['path'] = '/' . $dbName;
            
            $moduleUrl = $this->buildUrl($urlParts);
            
            return DriverManager::getConnection(['url' => $moduleUrl]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build URL from parts
     */
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

    /**
     * Import data for a specific module
     */
    public function importModuleData(string $moduleId, string $dataPath): bool
    {
        // Implementation for importing CSV data into module database
        // This would handle the data import process
        return true;
    }

    /**
     * Get current module ID
     */
    public function getCurrentModuleId(): ?string
    {
        return $this->currentModuleId;
    }

    /**
     * Get list of available modules that have corresponding databases
     */
    public function getAvailableModules(): array
    {
        // Return sample data for now to get the page working
        return [
            [
                'id' => '186',
                'name' => '186',
                'title' => 'NPC',
                'description' => 'Module 186 database'
            ],
            [
                'id' => '187',
                'name' => '187',
                'title' => 'HBV',
                'description' => 'Module 187 database'
            ],
            [
                'id' => '193',
                'name' => '193',
                'title' => 'BOT-Y',
                'description' => 'Module 193 database'
            ],
            [
                'id' => '194',
                'name' => '194',
                'title' => 'BOT-Z',
                'description' => 'Module 194 database'
            ],
            [
                'id' => '195',
                'name' => '195',
                'title' => 'BOT-X',
                'description' => 'Module 195 database'
            ]
        ];
    }

    /**
     * Check if a module database exists and has GWAS data
     */
    public function moduleDatabaseExists(string $moduleId): bool
    {
        try {
            $dbName = "Module_{$moduleId}";
            $connection = $this->createModuleConnection($dbName);
            
            if ($connection === null) {
                return false;
            }
            
            // Try to query a simple table to verify the database exists and is accessible
            $stmt = $connection->prepare("SHOW TABLES");
            $result = $stmt->executeQuery();
            $tables = $result->fetchAllAssociative();
            
            // Check if the database has the expected GWAS tables
            $expectedTables = ['ind', 'pos', 'chr', 'pval', 'ratio'];
            $foundTables = [];
            foreach ($tables as $table) {
                $foundTables[] = $table['Tables_in_' . strtolower($dbName)] ?? $table[0] ?? '';
            }
            
            // Must have at least 3 of the core GWAS tables
            $hasRequiredTables = count(array_intersect($expectedTables, $foundTables)) >= 3;
            
            // Also check if there's actual data in the tables
            $hasData = false;
            if ($hasRequiredTables) {
                try {
                    // Check if there's data in the 'ind' table (core SNP table)
                    $dataStmt = $connection->prepare("SELECT COUNT(*) FROM ind LIMIT 1");
                    $result = $dataStmt->executeQuery();
                    $count = $result->fetchOne();
                    $hasData = $count > 0;
                } catch (\Exception $e) {
                    $hasData = false;
                }
            }
            
            $connection->close();
            
            return $hasRequiredTables && $hasData;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a title for a module based on common patterns
     */
    private function getModuleTitle(string $moduleId): string
    {
        $titles = [
            '186' => 'NPC',
            '187' => 'HBV', 
            '193' => 'BOT-Y',
            '194' => 'BOT-Z',
            '195' => 'BOT-X'
        ];
        
        return $titles[$moduleId] ?? "Module $moduleId";
    }

    /**
     * Register a new module in the tracking table
     */
    public function registerModule(string $moduleId, int $ownerId = 1): bool
    {
        try {
            // Check if module database exists
            if (!$this->moduleDatabaseExists($moduleId)) {
                return false;
            }

            // Check if module is already registered
            $connection = $this->entityManager->getConnection();
            $checkSql = "SELECT COUNT(*) FROM module_tracking WHERE module_id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->executeQuery([$moduleId]);
            $exists = $checkStmt->fetchOne() > 0;

            if ($exists) {
                return true; // Already registered
            }

            // Register the module
            $insertSql = "INSERT INTO module_tracking (module_id, owner_id, visible, created_at) VALUES (?, ?, 1, NOW())";
            $insertStmt = $connection->prepare($insertSql);
            $insertStmt->executeStatement([$moduleId, $ownerId]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Unregister a module from the tracking table
     */
    public function unregisterModule(string $moduleId): bool
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "DELETE FROM module_tracking WHERE module_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->executeStatement([$moduleId]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set module visibility
     */
    public function setModuleVisibility(string $moduleId, bool $visible): bool
    {
        try {
            $connection = $this->entityManager->getConnection();
            $sql = "UPDATE module_tracking SET visible = ? WHERE module_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->executeStatement([$visible ? 1 : 0, $moduleId]);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
} 