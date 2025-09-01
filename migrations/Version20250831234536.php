<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250831234536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create genes table with build, chr, posstart, posend, strand, and gene columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE genes (
            build INT(8) NOT NULL,
            chr INT(8) NOT NULL,
            posstart INT(11) NOT NULL,
            posend INT(11) NOT NULL,
            strand INT(11) NOT NULL,
            gene VARCHAR(255) NOT NULL,
            INDEX idx_genes_chr (chr)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE genes');
    }
}
