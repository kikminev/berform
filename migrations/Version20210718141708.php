<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210718141708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP CONSTRAINT fk_39986e433be11523');
        $this->addSql('DROP INDEX idx_39986e433be11523');
        $this->addSql('ALTER TABLE album ADD default_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE album DROP default_image_id');
        $this->addSql('DROP INDEX idx_ab0788bb3be11523');
        $this->addSql('ALTER TABLE shot ADD default_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE shot DROP default_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE album ADD default_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE album DROP default_image');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT fk_39986e433be11523 FOREIGN KEY (default_image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_39986e433be11523 ON album (default_image_id)');
        $this->addSql('ALTER TABLE shot ADD default_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shot DROP default_image');
        $this->addSql('CREATE INDEX idx_ab0788bb3be11523 ON shot (default_image_id)');
    }
}
