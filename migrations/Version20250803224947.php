<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create module_tracking table for SONGBIRD database
 */
final class Version20250803224947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create module_tracking table to track available module databases';
    }

    public function up(Schema $schema): void
    {
        // Create module_tracking table in SONGBIRD database
        $this->addSql('CREATE TABLE module_tracking (
            id INT AUTO_INCREMENT NOT NULL,
            module_id VARCHAR(50) NOT NULL,
            owner_id INT NOT NULL,
            visible TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            PRIMARY KEY(id),
            UNIQUE KEY unique_module_id (module_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE module_tracking');
    }
}
