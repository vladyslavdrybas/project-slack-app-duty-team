<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517000628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_slack_user_skills (id UUID NOT NULL, owner_id UUID NOT NULL, skills JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_590D8B8D7E3C61F9 ON smfn_slack_user_skills (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_user_skills.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_user_skills.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_slack_user_skills ADD CONSTRAINT FK_590D8B8D7E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_slack_user_skills DROP CONSTRAINT FK_590D8B8D7E3C61F9');
        $this->addSql('DROP TABLE smfn_slack_user_skills');
    }
}
