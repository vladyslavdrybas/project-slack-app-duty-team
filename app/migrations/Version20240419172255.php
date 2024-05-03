<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419172255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_oauth_hash (hash VARCHAR(64) NOT NULL, owner_id UUID NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(owner_id, hash))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_469D1668D1B862B8 ON smfn_oauth_hash (hash)');
        $this->addSql('CREATE INDEX IDX_469D16687E3C61F9 ON smfn_oauth_hash (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_oauth_hash.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_oauth_hash ADD CONSTRAINT FK_469D16687E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_github_access_token ADD metadata JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX owner_email_idx ON smfn_github_access_token (owner_id, email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_oauth_hash DROP CONSTRAINT FK_469D16687E3C61F9');
        $this->addSql('DROP TABLE smfn_oauth_hash');
        $this->addSql('DROP INDEX owner_email_idx');
        $this->addSql('ALTER TABLE smfn_github_access_token DROP metadata');
    }
}
