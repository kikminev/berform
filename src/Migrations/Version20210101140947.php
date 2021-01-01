<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210101140947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE site ALTER facebook DROP NOT NULL');
        $this->addSql('ALTER TABLE site ALTER instagram DROP NOT NULL');
        $this->addSql('ALTER TABLE site ALTER linked_in DROP NOT NULL');
        $this->addSql('ALTER TABLE site ALTER twitter DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE site ALTER facebook SET NOT NULL');
        $this->addSql('ALTER TABLE site ALTER instagram SET NOT NULL');
        $this->addSql('ALTER TABLE site ALTER linked_in SET NOT NULL');
        $this->addSql('ALTER TABLE site ALTER twitter SET NOT NULL');
    }
}
