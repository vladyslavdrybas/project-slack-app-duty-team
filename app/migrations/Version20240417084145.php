<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240417084145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_refresh_tokens (id UUID NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(36) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48E4CBE8C74F2195 ON smfn_refresh_tokens (refresh_token)');
        $this->addSql('COMMENT ON COLUMN smfn_refresh_tokens.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_subscription (id UUID NOT NULL, subscriber_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, endDate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_payed BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1FA1A0D47808B1AD ON smfn_subscription (subscriber_id)');
        $this->addSql('CREATE INDEX IDX_1FA1A0D49B8CE200 ON smfn_subscription (subscription_plan_id)');
        $this->addSql('CREATE UNIQUE INDEX subscriber_subscription_plan ON smfn_subscription (subscriber_id, subscription_plan_id)');
        $this->addSql('COMMENT ON COLUMN smfn_subscription.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_subscription.subscriber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_subscription.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_subscription_plan (id UUID NOT NULL, title VARCHAR(36) NOT NULL, description VARCHAR(256) DEFAULT NULL, region VARCHAR(3) DEFAULT \'ALL\' NOT NULL, country VARCHAR(3) DEFAULT \'ALL\' NOT NULL, currency VARCHAR(3) DEFAULT \'USD\' NOT NULL, price INT NOT NULL, period VARCHAR(7) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX title_region_country_period ON smfn_subscription_plan (title, region, country, period)');
        $this->addSql('COMMENT ON COLUMN smfn_subscription_plan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE smfn_user (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(100) NOT NULL, firstname VARCHAR(100) DEFAULT NULL, lastname VARCHAR(100) DEFAULT NULL, password VARCHAR(100) NOT NULL, is_email_verified BOOLEAN DEFAULT false NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_banned BOOLEAN DEFAULT false NOT NULL, is_deleted BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EE7927C74 ON smfn_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8C5186EF85E0677 ON smfn_user (username)');
        $this->addSql('CREATE INDEX IDX_A8C5186E9A1887DC ON smfn_user (subscription_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE smfn_subscription ADD CONSTRAINT FK_1FA1A0D47808B1AD FOREIGN KEY (subscriber_id) REFERENCES smfn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_subscription ADD CONSTRAINT FK_1FA1A0D49B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES smfn_subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smfn_user ADD CONSTRAINT FK_A8C5186E9A1887DC FOREIGN KEY (subscription_id) REFERENCES smfn_subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_subscription DROP CONSTRAINT FK_1FA1A0D47808B1AD');
        $this->addSql('ALTER TABLE smfn_subscription DROP CONSTRAINT FK_1FA1A0D49B8CE200');
        $this->addSql('ALTER TABLE smfn_user DROP CONSTRAINT FK_A8C5186E9A1887DC');
        $this->addSql('DROP TABLE smfn_refresh_tokens');
        $this->addSql('DROP TABLE smfn_subscription');
        $this->addSql('DROP TABLE smfn_subscription_plan');
        $this->addSql('DROP TABLE smfn_user');
    }
}
