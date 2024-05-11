<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503155910 extends AbstractMigration
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
        $this->addSql('CREATE TABLE smfn_user (id UUID NOT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(100) NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, password VARCHAR(100) NOT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EE7927C74 ON smfn_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EF85E0677 ON smfn_user (username)');
        $this->addSql('COMMENT ON COLUMN smfn_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_github_access_token ADD CONSTRAINT FK_31111D627E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_oauth_hash ADD CONSTRAINT FK_469D16687E3C61F9 FOREIGN KEY (owner_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_github_access_token DROP CONSTRAINT FK_31111D627E3C61F9');
        $this->addSql('ALTER TABLE smfn_oauth_hash DROP CONSTRAINT FK_469D16687E3C61F9');
        $this->addSql('DROP TABLE smfn_github_access_token');
        $this->addSql('DROP TABLE smfn_oauth_hash');
        $this->addSql('DROP TABLE smfn_refresh_tokens');
        $this->addSql('DROP TABLE smfn_user');
    }
}
