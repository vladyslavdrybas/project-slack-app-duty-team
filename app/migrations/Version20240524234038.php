<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240524234038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_github_access_token (id UUID NOT NULL, owner_id UUID NOT NULL, access_token VARCHAR(256) NOT NULL, email VARCHAR(200) NOT NULL, username VARCHAR(200) NOT NULL, user_id VARCHAR(200) DEFAULT NULL, firstname VARCHAR(200) DEFAULT NULL, lastname VARCHAR(200) DEFAULT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, metadata JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_31111D62B6A2DD68 ON smfn_github_access_token (access_token)');
        $this->addSql('CREATE INDEX IDX_31111D627E3C61F9 ON smfn_github_access_token (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX owner_email_username_idx ON smfn_github_access_token (owner_id, email, username)');
        $this->addSql('COMMENT ON COLUMN smfn_github_access_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_github_access_token.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_oauth_hash (hash VARCHAR(64) NOT NULL, owner_id UUID NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(owner_id, hash))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_469D1668D1B862B8 ON smfn_oauth_hash (hash)');
        $this->addSql('CREATE INDEX IDX_469D16687E3C61F9 ON smfn_oauth_hash (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_oauth_hash.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_refresh_tokens (id UUID NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(36) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48E4CBE8C74F2195 ON smfn_refresh_tokens (refresh_token)');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_channel (id UUID NOT NULL, channel_id VARCHAR(180) NOT NULL, channel_name VARCHAR(180) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7DE5DC8772F5A1AA ON smfn_slack_channel (channel_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_command (id UUID NOT NULL, channel_id UUID NOT NULL, user_id UUID NOT NULL, text TEXT NOT NULL, command_name VARCHAR(237) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_51D6B81472F5A1AA ON smfn_slack_command (channel_id)');
        $this->addSql('CREATE INDEX IDX_51D6B814A76ED395 ON smfn_slack_command (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_command.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_slack_user (id UUID NOT NULL, owner_id UUID NOT NULL, user_id VARCHAR(100) NOT NULL, user_name VARCHAR(255) NOT NULL, team_id VARCHAR(100) NOT NULL, team_domain VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, full_name VARCHAR(255) DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, timezone_label VARCHAR(255) DEFAULT NULL, timezone_offset INT DEFAULT 0 NOT NULL, title VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, avatar_hash VARCHAR(100) DEFAULT NULL, color VARCHAR(6) DEFAULT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, is_admin BOOLEAN DEFAULT false NOT NULL, is_owner BOOLEAN DEFAULT false NOT NULL, is_primary_owner BOOLEAN DEFAULT false NOT NULL, is_restricted BOOLEAN DEFAULT false NOT NULL, is_ultra_restricted BOOLEAN DEFAULT false NOT NULL, is_bot BOOLEAN DEFAULT false NOT NULL, is_app_user BOOLEAN DEFAULT false NOT NULL, is_email_confirmed BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A77CB97A76ED395 ON smfn_slack_user (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A77CB97296CD8AE ON smfn_slack_user (team_id)');
        $this->addSql('CREATE INDEX IDX_A77CB977E3C61F9 ON smfn_slack_user (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_slack_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_slack_user.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_user (id UUID NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(100) NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, password VARCHAR(100) NOT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EE7927C74 ON smfn_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EF85E0677 ON smfn_user (username)');
        $this->addSql('COMMENT ON COLUMN smfn_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_user_skills (id UUID NOT NULL, owner_id UUID NOT NULL, skills JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B12410FC7E3C61F9 ON smfn_user_skills (owner_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_skills.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_skills.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_user_time_off (id UUID NOT NULL, user_id UUID NOT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F68204EA76ED395 ON smfn_user_time_off (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_github_access_token ADD CONSTRAINT FK_31111D627E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_oauth_hash ADD CONSTRAINT FK_469D16687E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_slack_command ADD CONSTRAINT FK_51D6B81472F5A1AA FOREIGN KEY (channel_id) REFERENCES smfn_slack_channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_slack_command ADD CONSTRAINT FK_51D6B814A76ED395 FOREIGN KEY (user_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_slack_user ADD CONSTRAINT FK_A77CB977E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user_skills ADD CONSTRAINT FK_B12410FC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user_time_off ADD CONSTRAINT FK_8F68204EA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_github_access_token DROP CONSTRAINT FK_31111D627E3C61F9');
        $this->addSql('ALTER TABLE smfn_oauth_hash DROP CONSTRAINT FK_469D16687E3C61F9');
        $this->addSql('ALTER TABLE smfn_slack_command DROP CONSTRAINT FK_51D6B81472F5A1AA');
        $this->addSql('ALTER TABLE smfn_slack_command DROP CONSTRAINT FK_51D6B814A76ED395');
        $this->addSql('ALTER TABLE smfn_slack_user DROP CONSTRAINT FK_A77CB977E3C61F9');
        $this->addSql('ALTER TABLE smfn_user_skills DROP CONSTRAINT FK_B12410FC7E3C61F9');
        $this->addSql('ALTER TABLE smfn_user_time_off DROP CONSTRAINT FK_8F68204EA76ED395');
        $this->addSql('DROP TABLE smfn_github_access_token');
        $this->addSql('DROP TABLE smfn_oauth_hash');
        $this->addSql('DROP TABLE smfn_refresh_tokens');
        $this->addSql('DROP TABLE smfn_slack_channel');
        $this->addSql('DROP TABLE smfn_slack_command');
        $this->addSql('DROP TABLE smfn_slack_user');
        $this->addSql('DROP TABLE smfn_user');
        $this->addSql('DROP TABLE smfn_user_skills');
        $this->addSql('DROP TABLE smfn_user_time_off');
    }
}
