<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210103161558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE page ADD default_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page DROP default_image');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6203BE11523 FOREIGN KEY (default_image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6203BE11523 ON page (default_image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE page DROP CONSTRAINT FK_140AB6203BE11523');
        $this->addSql('DROP INDEX UNIQ_140AB6203BE11523');
        $this->addSql('ALTER TABLE page ADD default_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE page DROP default_image_id');
    }
}
