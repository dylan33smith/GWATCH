<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service for managing manhattan plot data
 * Handles storage and retrieval of PNG images and metadata
 */
class ManhattanPlotService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Create a connection to a specific module database
     */
    private function createModuleConnection(string $moduleId): Connection
    {
        // If moduleId is just a number, convert to full database name
        $databaseName = is_numeric($moduleId) ? 'Module_' . $moduleId : $moduleId;
        
        try {
            $baseUrl = $this->params->get('app.database_url');
            $urlParts = parse_url($baseUrl);
            $urlParts['path'] = '/' . $databaseName;
            
            $moduleUrl = $this->buildUrl($urlParts);
            return DriverManager::getConnection(['url' => $moduleUrl]);
        } catch (\Exception $e) {
            throw new \Exception("Could not connect to module database {$databaseName}: " . $e->getMessage());
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
            if (isset($parts['port'])) {
                $url .= ':' . $parts['port'];
            }
        }
        
        if (isset($parts['path'])) {
            $url .= $parts['path'];
        }
        
        if (isset($parts['query'])) {
            $url .= '?' . $parts['query'];
        }
        
        return $url;
    }
    
    /**
     * Store PNG data for a manhattan plot
     */
    public function storeManhattanPlotPng(int $moduleId, int $testNumber, string $pngData): bool
    {
        $connection = $this->createModuleConnection($moduleId);
        
        try {
            $sql = "INSERT INTO mplot_png (test_number, png) VALUES (?, ?) ON DUPLICATE KEY UPDATE png = VALUES(png)";
            $connection->executeStatement($sql, [$testNumber, $pngData]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to store PNG data: " . $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    /**
     * Store metadata for a manhattan plot
     */
    public function storeManhattanPlotMetadata(int $moduleId, array $metadata): bool
    {
        if (empty($metadata)) {
            return true;
        }

        $connection = $this->createModuleConnection($moduleId);
        
        try {
            // Clear existing metadata for this test
            $testNumber = $metadata[0]['test_number'];
            $connection->executeStatement("DELETE FROM mplot WHERE test_number = ?", [$testNumber]);
            
            // Insert new metadata
            $sql = "INSERT INTO mplot (ind, test_number, chr, nrow, coordX, coordY) VALUES (?, ?, ?, ?, ?, ?)";
            foreach ($metadata as $row) {
                $connection->executeStatement($sql, [
                    $row['ind'], $row['test_number'], $row['chr'], 
                    $row['nrow'], $row['coordX'], $row['coordY']
                ]);
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Failed to store metadata: " . $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    /**
     * Get PNG data for a manhattan plot
     */
    public function getManhattanPlotPng(string $moduleId, int $testNumber): ?string
    {
        $connection = $this->createModuleConnection($moduleId);
        
        try {
            $result = $connection->executeQuery("SELECT png FROM mplot_png WHERE test_number = ?", [$testNumber]);
            $row = $result->fetchAssociative();
            return $row ? $row['png'] : null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve PNG data: " . $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    /**
     * Get metadata for a manhattan plot
     */
    public function getManhattanPlotMetadata(string $moduleId, int $testNumber): array
    {
        $connection = $this->createModuleConnection($moduleId);
        
        try {
            $result = $connection->executeQuery("SELECT * FROM mplot WHERE test_number = ? ORDER BY coordX", [$testNumber]);
            return $result->fetchAllAssociative();
        } catch (\Exception $e) {
            throw new \Exception("Failed to retrieve metadata: " . $e->getMessage());
        } finally {
            $connection->close();
        }
    }

    /**
     * Check if a manhattan plot exists for a test
     */
    public function manhattanPlotExists(string $moduleId, int $testNumber): bool
    {
        $connection = $this->createModuleConnection($moduleId);
        
        try {
            $result = $connection->executeQuery("SELECT COUNT(*) as count FROM mplot_png WHERE test_number = ?", [$testNumber]);
            $row = $result->fetchAssociative();
            return $row && $row['count'] > 0;
        } catch (\Exception $e) {
            return false;
        } finally {
            $connection->close();
        }
    }
}
