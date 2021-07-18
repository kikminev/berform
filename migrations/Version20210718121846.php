<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210718121846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP CONSTRAINT fk_8c9f36101137abcf');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT fk_8c9f36104b89032c');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT fk_8c9f3610c274538a');
        $this->addSql('ALTER TABLE file DROP CONSTRAINT fk_8c9f3610c4663e4');
        $this->addSql('DROP INDEX idx_8c9f3610c4663e4');
        $this->addSql('DROP INDEX idx_8c9f3610c274538a');
        $this->addSql('DROP INDEX idx_8c9f36101137abcf');
        $this->addSql('DROP INDEX idx_8c9f36104b89032c');
        $this->addSql('ALTER TABLE file DROP page_id');
        $this->addSql('ALTER TABLE file DROP album_id');
        $this->addSql('ALTER TABLE file DROP post_id');
        $this->addSql('ALTER TABLE file DROP shot_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE file ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD album_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD shot_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT fk_8c9f36101137abcf FOREIGN KEY (album_id) REFERENCES album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT fk_8c9f36104b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT fk_8c9f3610c274538a FOREIGN KEY (shot_id) REFERENCES shot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT fk_8c9f3610c4663e4 FOREIGN KEY (page_id) REFERENCES page (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8c9f3610c4663e4 ON file (page_id)');
        $this->addSql('CREATE INDEX idx_8c9f3610c274538a ON file (shot_id)');
        $this->addSql('CREATE INDEX idx_8c9f36101137abcf ON file (album_id)');
        $this->addSql('CREATE INDEX idx_8c9f36104b89032c ON file (post_id)');
    }
}
