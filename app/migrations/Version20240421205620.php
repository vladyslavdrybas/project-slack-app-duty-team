<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421205620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_github_user_contributions (id UUID NOT NULL, owner_id UUID NOT NULL, year INT NOT NULL, total INT DEFAULT 0 NOT NULL, weeks JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3B8494627E3C61F9 ON smfn_github_user_contributions (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX owner_year_idx ON smfn_github_user_contributions (owner_id, year)');
        $this->addSql('COMMENT ON COLUMN smfn_github_user_contributions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_github_user_contributions.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_github_user_contributions ADD CONSTRAINT FK_3B8494627E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_github_user_contributions DROP CONSTRAINT FK_3B8494627E3C61F9');
        $this->addSql('DROP TABLE smfn_github_user_contributions');
    }
}
