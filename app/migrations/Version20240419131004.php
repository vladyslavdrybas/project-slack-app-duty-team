<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419131004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_github_access_token (id UUID NOT NULL, owner_id UUID NOT NULL, access_token VARCHAR(256) NOT NULL, email VARCHAR(200) NOT NULL, username VARCHAR(200) NOT NULL, firstname VARCHAR(200) DEFAULT NULL, lastname VARCHAR(200) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31111D62B6A2DD68 ON smfn_github_access_token (access_token)');
        $this->addSql('CREATE INDEX IDX_31111D627E3C61F9 ON smfn_github_access_token (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_github_access_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_github_access_token.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_github_access_token ADD CONSTRAINT FK_31111D627E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_github_access_token DROP CONSTRAINT FK_31111D627E3C61F9');
        $this->addSql('DROP TABLE smfn_github_access_token');
    }
}
