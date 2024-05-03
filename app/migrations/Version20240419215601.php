<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419215601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX owner_email_idx');
        $this->addSql('ALTER TABLE smfn_github_access_token ADD expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX owner_email_username_idx ON smfn_github_access_token (owner_id, email, username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX owner_email_username_idx');
        $this->addSql('ALTER TABLE smfn_github_access_token DROP expire_at');
        $this->addSql('CREATE UNIQUE INDEX owner_email_idx ON smfn_github_access_token (owner_id, email)');
    }
}
