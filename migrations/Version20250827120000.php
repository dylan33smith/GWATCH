<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250827120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add mplots and significant_points tables to module databases';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS `mplots` (
            `test_id` int(11) NOT NULL COMMENT \'Test ID from col table\',
            `test_name` varchar(255) NOT NULL COMMENT \'Test name from col.test\',
            `plot_image` longblob NOT NULL COMMENT \'PNG image data\',
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'Creation timestamp\',
            `plot_width` int(11) NOT NULL COMMENT \'Image width in pixels\',
            `plot_height` int(11) NOT NULL COMMENT \'Image height in pixels\',
            PRIMARY KEY (`test_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=\'Manhattan plot images for each test\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `mplots`');
    }
}
