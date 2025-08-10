<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250809195143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (user_id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(88) NOT NULL, mail VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, created_at INT NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP INDEX unique_module_id ON module_tracking');
        $this->addSql('ALTER TABLE module_tracking ADD public TINYINT(1) NOT NULL, DROP visible, CHANGE module_id moduleId VARCHAR(50) NOT NULL, CHANGE owner_id ownerId INT NOT NULL, CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CCBCB1E2A5ED6481 ON module_tracking (moduleId)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX UNIQ_CCBCB1E2A5ED6481 ON module_tracking');
        $this->addSql('ALTER TABLE module_tracking ADD visible TINYINT(1) DEFAULT 1 NOT NULL, DROP public, CHANGE moduleId module_id VARCHAR(50) NOT NULL, CHANGE ownerId owner_id INT NOT NULL, CHANGE createdAt created_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_module_id ON module_tracking (module_id)');
    }
}
