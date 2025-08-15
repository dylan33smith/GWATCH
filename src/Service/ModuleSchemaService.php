<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

/**
 * Service for managing module database table schemas
 * Centralizes all table creation SQL and provides a clean interface
 */
class ModuleSchemaService
{
    /**
     * All module table schemas
     */
    private array $tableSchemas = [
        'chr' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `chr` (
                `chr` INT NOT NULL,
                `chrname` VARCHAR(255) NOT NULL,
                `len` INT NOT NULL,
                PRIMARY KEY (`chr`),
                INDEX `idx_chrname` (`chrname`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chromosome information'",
            'description' => 'Chromosome table with chromosome number, name, and length'
        ],
        
        'chrsupp' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `chrsupp` (
                `chr` INT NOT NULL,
                `chroff` INT NOT NULL,
                `chrlen` INT NOT NULL,
                PRIMARY KEY (`chr`),
                INDEX `idx_chr` (`chr`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Chromosome supplement information'",
            'description' => 'Chromosome supplement table with offset and length data'
        ],
        
        'col' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `col` (
                `col` INT NOT NULL,
                `test` VARCHAR(255) NULL,
                `refTable` VARCHAR(255) NOT NULL,
                `refCol` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`col`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Column reference information'",
            'description' => 'Column reference table with test and reference data'
        ],
        
        'ind' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `ind` (
                `chr` INT NOT NULL,
                `nrow` INT NOT NULL,
                `ind` INT NOT NULL,
                PRIMARY KEY (`chr`, `nrow`),
                INDEX `idx_ind` (`ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Index information'",
            'description' => 'Index table with chromosome, row, and index data'
        ],
        
        'r_pval' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `r_pval` (
                `v_ind` INT NOT NULL,
                `r_pval` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='R p-value information'",
            'description' => 'R p-value table with variant index and p-value data'
        ],
        
        'r_ratio' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `r_ratio` (
                `v_ind` INT NOT NULL,
                `r_ratio` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='R ratio information'",
            'description' => 'R ratio table with variant index and ratio data'
        ],
        
        'v_ind' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `v_ind` (
                `ind` INT NOT NULL,
                `col` INT NOT NULL,
                `v_ind` INT NOT NULL,
                PRIMARY KEY (`ind`, `col`),
                INDEX `idx_v_ind` (`v_ind`),
                INDEX `idx_ind` (`ind`),
                INDEX `idx_col` (`col`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Variant index information'",
            'description' => 'Variant index table with index, column, and variant data'
        ],
        
        'pos' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `pos` (
                `v_ind` INT NOT NULL,
                `pos` INT NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Position information'",
            'description' => 'Position table with variant index and position data'
        ],
        
        'alias' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `alias` (
                `v_ind` INT NOT NULL,
                `alias` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alias information'",
            'description' => 'Alias table with variant index and alias data'
        ],
        
        'allele' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `allele` (
                `v_ind` INT NOT NULL,
                `allele` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Allele information'",
            'description' => 'Allele table with variant index and allele data'
        ],
        
        'maf' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `maf` (
                `v_ind` INT NOT NULL,
                `maf` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Minor allele frequency information'",
            'description' => 'MAF table with variant index and frequency data'
        ],
        
        'pval' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `pval` (
                `v_ind` INT NOT NULL,
                `pval` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='P-value information'",
            'description' => 'P-value table with variant index and p-value data'
        ],
        
        'ratio' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `ratio` (
                `v_ind` INT NOT NULL,
                `ratio` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ratio information'",
            'description' => 'Ratio table with variant index and ratio data'
        ],
        
        'row' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `row` (
                `ind` INT NOT NULL,
                `row` INT NOT NULL,
                PRIMARY KEY (`ind`),
                INDEX `idx_ind` (`ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Row information'",
            'description' => 'Row table with index and row data'
        ],
        
        'val' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `val` (
                `v_ind` INT NOT NULL,
                `val` DOUBLE NOT NULL,
                PRIMARY KEY (`v_ind`),
                INDEX `idx_v_ind` (`v_ind`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Value information'",
            'description' => 'Value table with variant index and value data'
        ],
        
        'radius_ind' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `radius_ind` (
                `radius_ind` int(2) unsigned NOT NULL COMMENT 'Radius index',
                `radius_type` varchar(31) NOT NULL COMMENT 'Radius type',
                `radius_val` int(4) unsigned NOT NULL COMMENT 'Radius value',
                KEY `idx_radius_ind` (`radius_ind`),
                KEY `idx_radius_type` (`radius_type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radius index information'",
            'description' => 'Radius index table with radius index, type, and value data'
        ],
        
        'top_hits' => [
            'sql' => "CREATE TABLE IF NOT EXISTS `top_hits` (
                `bits` int(4) NOT NULL COMMENT 'Analysis type flags',
                `radius_ind` int(2) unsigned NOT NULL COMMENT 'Radius index (1,2,3...)',
                `v_ind` int(8) unsigned NOT NULL COMMENT 'Variant index',
                `r_density` int(4) unsigned NOT NULL COMMENT 'Ranked density',
                `r_naive_p` int(4) unsigned NOT NULL COMMENT 'Ranked naive p-value',
                `left_ind` int(8) unsigned NOT NULL COMMENT 'Left genomic boundary',
                `right_ind` int(8) unsigned NOT NULL COMMENT 'Right genomic boundary',
                `left_cnt` int(4) unsigned NOT NULL COMMENT 'Left count',
                `right_cnt` int(4) unsigned NOT NULL COMMENT 'Right count',
                `density` double NULL COMMENT 'Density value',
                `naive_p` double NULL COMMENT 'Naive p-value',
                `adj_p` double NULL COMMENT 'Adjusted p-value',
                `cal_p` double NULL COMMENT 'Calibrated p-value',
                PRIMARY KEY (`bits`, `radius_ind`, `v_ind`),
                KEY `idx_bits` (`bits`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Top hits analysis results'",
            'description' => 'Top hits table with analysis results and genomic boundaries'
        ]
    ];

    /**
     * Create a specific table in the module database
     */
    public function createTable(Connection $connection, string $tableName): void
    {
        if (!isset($this->tableSchemas[$tableName])) {
            throw new \InvalidArgumentException("Unknown table schema: {$tableName}");
        }

        try {
            $sql = $this->tableSchemas[$tableName]['sql'];
            $connection->executeStatement($sql);
        } catch (Exception $e) {
            throw new \Exception("Failed to create table {$tableName}: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all available table names
     */
    public function getAvailableTables(): array
    {
        return array_keys($this->tableSchemas);
    }

    /**
     * Get table description
     */
    public function getTableDescription(string $tableName): ?string
    {
        return $this->tableSchemas[$tableName]['description'] ?? null;
    }

    /**
     * Check if table schema exists
     */
    public function hasTableSchema(string $tableName): bool
    {
        return isset($this->tableSchemas[$tableName]);
    }

    /**
     * Get table schema SQL
     */
    public function getTableSchema(string $tableName): ?string
    {
        return $this->tableSchemas[$tableName]['sql'] ?? null;
    }
}
