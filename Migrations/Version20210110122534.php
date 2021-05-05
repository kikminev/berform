<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210110122534 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE album ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE album ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE site ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE site ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE user_customer ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE user_customer ALTER updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE file ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE file ALTER updated_at SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_customer ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE user_customer ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE album ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE album ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE site ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE site ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE file ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE file ALTER updated_at DROP NOT NULL');
    }
}
