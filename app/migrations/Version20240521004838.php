<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521004838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smfn_user_time_off (id UUID NOT NULL, user_id UUID NOT NULL, start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, "end" TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F68204EA76ED395 ON smfn_user_time_off (user_id)');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off.start IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN smfn_user_time_off."end" IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE smfn_user_time_off ADD CONSTRAINT FK_8F68204EA76ED395 FOREIGN KEY (user_id) REFERENCES smfn_slack_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE smfn_user_time_off DROP CONSTRAINT FK_8F68204EA76ED395');
        $this->addSql('DROP TABLE smfn_user_time_off');
    }
}
