<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250814195650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add radius_ind and top_hits tables to module databases';
    }

    public function up(Schema $schema): void
    {
        // Create radius_ind table for module databases
        $this->addSql('CREATE TABLE IF NOT EXISTS `radius_ind` (
            `radius_ind` int(2) unsigned NOT NULL COMMENT \'Radius index\',
            `radius_type` varchar(31) NOT NULL COMMENT \'Radius type\',
            `radius_val` int(4) unsigned NOT NULL COMMENT \'Radius value\',
            PRIMARY KEY (`radius_ind`),
            KEY `idx_radius_type` (`radius_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'Radius index information\'');

        // Create top_hits table for module databases
        $this->addSql('CREATE TABLE IF NOT EXISTS `top_hits` (
            `bits` int(4) NOT NULL COMMENT \'Analysis type flags\',
            `radius_ind` int(2) unsigned NOT NULL COMMENT \'Radius index (1,2,3...)\',
            `v_ind` int(8) unsigned NOT NULL COMMENT \'Variant index\',
            `r_density` int(4) unsigned NOT NULL COMMENT \'Ranked density\',
            `r_naive_p` int(4) unsigned NOT NULL COMMENT \'Ranked naive p-value\',
            `left_ind` int(8) unsigned NOT NULL COMMENT \'Left genomic boundary\',
            `right_ind` int(8) unsigned NOT NULL COMMENT \'Right genomic boundary\',
            `left_cnt` int(4) unsigned NOT NULL COMMENT \'Left count\',
            `right_cnt` int(4) unsigned NOT NULL COMMENT \'Right count\',
            `density` double NULL COMMENT \'Density value\',
            `naive_p` double NULL COMMENT \'Naive p-value\',
            `adj_p` double NULL COMMENT \'Adjusted p-value\',
            `cal_p` double NULL COMMENT \'Calibrated p-value\',
            PRIMARY KEY (`bits`, `radius_ind`, `v_ind`),
            KEY `idx_bits` (`bits`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'Top hits analysis results\'');

    }

    public function down(Schema $schema): void
    {
        // Drop top_hits table
        $this->addSql('DROP TABLE IF EXISTS `top_hits`');
        
        // Drop radius_ind table
        $this->addSql('DROP TABLE IF EXISTS `radius_ind`');
    }
}
