<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210101164432 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE page_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE page (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, sequence_order INT DEFAULT NULL, locale VARCHAR(255) NOT NULL, custom_css TEXT DEFAULT NULL, is_active BOOLEAN DEFAULT NULL, is_deleted BOOLEAN DEFAULT NULL, default_image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE site ADD user_customer_id INT');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E43A8E0A66 FOREIGN KEY (user_customer_id) REFERENCES user_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_694309E43A8E0A66 ON site (user_customer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE page_id_seq CASCADE');
        $this->addSql('DROP TABLE page');
        $this->addSql('ALTER TABLE site DROP CONSTRAINT FK_694309E43A8E0A66');
        $this->addSql('DROP INDEX IDX_694309E43A8E0A66');
        $this->addSql('ALTER TABLE site DROP user_customer_id');
    }
}
