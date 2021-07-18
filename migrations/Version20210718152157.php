<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210718152157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post DROP CONSTRAINT fk_5a8a6c8d3be11523');
        $this->addSql('ALTER TABLE post ADD default_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP default_image_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post ADD default_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP default_image');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT fk_5a8a6c8d3be11523 FOREIGN KEY (default_image_id) REFERENCES file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
