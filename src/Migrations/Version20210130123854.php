<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210130123854 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE subscription ADD user_customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP "user"');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D33A8E0A66 FOREIGN KEY (user_customer_id) REFERENCES user_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A3C664D33A8E0A66 ON subscription (user_customer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D33A8E0A66');
        $this->addSql('DROP INDEX IDX_A3C664D33A8E0A66');
        $this->addSql('ALTER TABLE subscription ADD "user" VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP user_customer_id');
        $this->addSql('ALTER TABLE subscription DROP expires_at');
    }
}
