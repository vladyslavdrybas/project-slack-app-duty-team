<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517002626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smfn_slack_user_skills DROP CONSTRAINT fk_590d8b8d7e3c61f9');
        $this->addSql('DROP INDEX uniq_590d8b8d7e3c61f9');
        $this->addSql('ALTER TABLE smfn_slack_user_skills RENAME COLUMN owner_id TO slack_user_id');
        $this->addSql('ALTER TABLE smfn_slack_user_skills ADD CONSTRAINT FK_590D8B8DE6AA7332 FOREIGN KEY (slack_user_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_590D8B8DE6AA7332 ON smfn_slack_user_skills (slack_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_slack_user_skills DROP CONSTRAINT FK_590D8B8DE6AA7332');
        $this->addSql('DROP INDEX UNIQ_590D8B8DE6AA7332');
        $this->addSql('ALTER TABLE smfn_slack_user_skills RENAME COLUMN slack_user_id TO owner_id');
        $this->addSql('ALTER TABLE smfn_slack_user_skills ADD CONSTRAINT fk_590d8b8d7e3c61f9 FOREIGN KEY (owner_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_590d8b8d7e3c61f9 ON smfn_slack_user_skills (owner_id)');
    }
}
