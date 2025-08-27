<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Add significant_points table to module databases
 */
final class Version20250827120001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add significant_points table to module databases';
    }

    public function up(Schema $schema): void
    {
        // Create significant_points table for module databases
        $this->addSql('CREATE TABLE IF NOT EXISTS `significant_points` (
            `test_id` int(11) NOT NULL COMMENT \'Foreign key to mplots.test_id\',
            `snp_ind` int(11) NOT NULL COMMENT \'SNP index from ind.ind\',
            `x_pixel` int(11) NOT NULL COMMENT \'X-coordinate on plot\',
            `y_pixel` int(11) NOT NULL COMMENT \'Y-coordinate on plot\',
            `p_value` double NOT NULL COMMENT \'Original p-value\',
            `neg_log_p` double NOT NULL COMMENT \'-log10(p-value)\',
            `chromosome` int(11) NOT NULL COMMENT \'Chromosome number\',
            PRIMARY KEY (`test_id`, `snp_ind`),
            KEY `idx_chromosome` (`chromosome`),
            KEY `idx_neg_log_p` (`neg_log_p`),
            CONSTRAINT `fk_significant_points_test` FOREIGN KEY (`test_id`) REFERENCES `mplots` (`test_id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'Significant points coordinates for Manhattan plots\'');
    }

    public function down(Schema $schema): void
    {
        // Drop significant_points table first (due to foreign key constraint)
        $this->addSql('DROP TABLE IF EXISTS `significant_points`');
    }
}
