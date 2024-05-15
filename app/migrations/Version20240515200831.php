<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515200831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_slack_channel (id UUID NOT NULL, channel_id VARCHAR(180) NOT NULL, channel_name VARCHAR(180) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DE5DC8772F5A1AA ON smfn_slack_channel (channel_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_command (id UUID NOT NULL, channel_id UUID NOT NULL, team_id UUID NOT NULL, user_id UUID NOT NULL, data JSON NOT NULL, command_name VARCHAR(237) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51D6B81472F5A1AA ON smfn_slack_command (channel_id)');
        $this->addSql('CREATE INDEX IDX_51D6B814296CD8AE ON smfn_slack_command (team_id)');
        $this->addSql('CREATE INDEX IDX_51D6B814A76ED395 ON smfn_slack_command (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.team_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_team (id UUID NOT NULL, team_id VARCHAR(180) NOT NULL, team_domain VARCHAR(180) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4304BBC1296CD8AE ON smfn_slack_team (team_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_team.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_user (id UUID NOT NULL, user_id VARCHAR(180) NOT NULL, user_name VARCHAR(180) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A77CB97A76ED395 ON smfn_slack_user (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_slack_command ADD CONSTRAINT FK_51D6B81472F5A1AA FOREIGN KEY (channel_id) REFERENCES smfn_slack_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_slack_command ADD CONSTRAINT FK_51D6B814296CD8AE FOREIGN KEY (team_id) REFERENCES smfn_slack_team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_slack_command ADD CONSTRAINT FK_51D6B814A76ED395 FOREIGN KEY (user_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_slack_command DROP CONSTRAINT FK_51D6B81472F5A1AA');
        $this->addSql('ALTER TABLE smfn_slack_command DROP CONSTRAINT FK_51D6B814296CD8AE');
        $this->addSql('ALTER TABLE smfn_slack_command DROP CONSTRAINT FK_51D6B814A76ED395');
        $this->addSql('DROP TABLE smfn_slack_channel');
        $this->addSql('DROP TABLE smfn_slack_command');
        $this->addSql('DROP TABLE smfn_slack_team');
        $this->addSql('DROP TABLE smfn_slack_user');
    }
}
