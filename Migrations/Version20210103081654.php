<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210103081654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE page ADD translated_menu_link JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD translated_content JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD translated_keywords JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD translated_meta_description JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE page ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE page DROP translated_menu_link');
        $this->addSql('ALTER TABLE page DROP translated_content');
        $this->addSql('ALTER TABLE page DROP translated_keywords');
        $this->addSql('ALTER TABLE page DROP translated_meta_description');
        $this->addSql('ALTER TABLE page DROP created_at');
        $this->addSql('ALTER TABLE page DROP updated_at');
    }
}
