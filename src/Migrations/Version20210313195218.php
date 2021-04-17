<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313195218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE "order" ADD user_customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993983A8E0A66 FOREIGN KEY (user_customer_id) REFERENCES user_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F52993983A8E0A66 ON "order" (user_customer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993983A8E0A66');
        $this->addSql('DROP INDEX IDX_F52993983A8E0A66');
        $this->addSql('ALTER TABLE "order" DROP user_customer_id');
    }
}
