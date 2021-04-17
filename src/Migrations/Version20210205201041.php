<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210205201041 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product ALTER currency_id SET NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD38248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04AD38248176 ON product (currency_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04AD38248176');
        $this->addSql('DROP INDEX IDX_D34A04AD38248176');
        $this->addSql('ALTER TABLE product ALTER currency_id DROP NOT NULL');
    }
}
