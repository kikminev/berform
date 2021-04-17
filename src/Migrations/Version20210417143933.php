<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210417143933 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE message_id_seq');
        $this->addSql('SELECT setval(\'message_id_seq\', (SELECT MAX(id) FROM message))');
        $this->addSql('ALTER TABLE message ALTER id SET DEFAULT nextval(\'message_id_seq\')');
        $this->addSql('CREATE SEQUENCE shot_id_seq');
        $this->addSql('SELECT setval(\'shot_id_seq\', (SELECT MAX(id) FROM shot))');
        $this->addSql('ALTER TABLE shot ALTER id SET DEFAULT nextval(\'shot_id_seq\')');
        $this->addSql('CREATE SEQUENCE post_id_seq');
        $this->addSql('SELECT setval(\'post_id_seq\', (SELECT MAX(id) FROM post))');
        $this->addSql('ALTER TABLE post ALTER id SET DEFAULT nextval(\'post_id_seq\')');
        $this->addSql('CREATE SEQUENCE order_id_seq');
        $this->addSql('SELECT setval(\'order_id_seq\', (SELECT MAX(id) FROM "order"))');
        $this->addSql('ALTER TABLE "order" ALTER id SET DEFAULT nextval(\'order_id_seq\')');
        $this->addSql('CREATE SEQUENCE album_id_seq');
        $this->addSql('SELECT setval(\'album_id_seq\', (SELECT MAX(id) FROM album))');
        $this->addSql('ALTER TABLE album ALTER id SET DEFAULT nextval(\'album_id_seq\')');
        $this->addSql('CREATE SEQUENCE site_id_seq');
        $this->addSql('SELECT setval(\'site_id_seq\', (SELECT MAX(id) FROM site))');
        $this->addSql('ALTER TABLE site ALTER id SET DEFAULT nextval(\'site_id_seq\')');
        $this->addSql('CREATE SEQUENCE user_customer_id_seq');
        $this->addSql('SELECT setval(\'user_customer_id_seq\', (SELECT MAX(id) FROM user_customer))');
        $this->addSql('ALTER TABLE user_customer ALTER id SET DEFAULT nextval(\'user_customer_id_seq\')');
        $this->addSql('CREATE SEQUENCE domain_id_seq');
        $this->addSql('SELECT setval(\'domain_id_seq\', (SELECT MAX(id) FROM domain))');
        $this->addSql('ALTER TABLE domain ALTER id SET DEFAULT nextval(\'domain_id_seq\')');
        $this->addSql('CREATE SEQUENCE transaction_id_seq');
        $this->addSql('SELECT setval(\'transaction_id_seq\', (SELECT MAX(id) FROM transaction))');
        $this->addSql('ALTER TABLE transaction ALTER id SET DEFAULT nextval(\'transaction_id_seq\')');
        $this->addSql('CREATE SEQUENCE product_id_seq');
        $this->addSql('SELECT setval(\'product_id_seq\', (SELECT MAX(id) FROM product))');
        $this->addSql('ALTER TABLE product ALTER id SET DEFAULT nextval(\'product_id_seq\')');
        $this->addSql('CREATE SEQUENCE currency_id_seq');
        $this->addSql('SELECT setval(\'currency_id_seq\', (SELECT MAX(id) FROM currency))');
        $this->addSql('ALTER TABLE currency ALTER id SET DEFAULT nextval(\'currency_id_seq\')');
        $this->addSql('CREATE SEQUENCE subscription_id_seq');
        $this->addSql('SELECT setval(\'subscription_id_seq\', (SELECT MAX(id) FROM subscription))');
        $this->addSql('ALTER TABLE subscription ALTER id SET DEFAULT nextval(\'subscription_id_seq\')');
        $this->addSql('CREATE SEQUENCE page_id_seq');
        $this->addSql('SELECT setval(\'page_id_seq\', (SELECT MAX(id) FROM page))');
        $this->addSql('ALTER TABLE page ALTER id SET DEFAULT nextval(\'page_id_seq\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE post ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE message ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE album ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE currency ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE shot ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE "order" ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE domain ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE product ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE page ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE transaction ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE subscription ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_customer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE site ALTER id DROP DEFAULT');
    }
}
