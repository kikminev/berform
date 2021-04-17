<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210103075613 extends AbstractMigration
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
    }
}
