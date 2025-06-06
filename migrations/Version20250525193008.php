<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525193008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE auction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, winner_bid_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , end_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , bid_cost INTEGER NOT NULL, status VARCHAR(10) NOT NULL, payment_received BOOLEAN DEFAULT 0 NOT NULL, product_delivered BOOLEAN DEFAULT 0 NOT NULL, CONSTRAINT FK_DEE4F5934584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DEE4F59361C2EF79 FOREIGN KEY (winner_bid_id) REFERENCES bid (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DEE4F5934584665A ON auction (product_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_DEE4F59361C2EF79 ON auction (winner_bid_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE bid (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, auction_id INTEGER NOT NULL, user_id INTEGER NOT NULL, price_cents INTEGER NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_4AF2B3F357B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4AF2B3F3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4AF2B3F357B8F0DE ON bid (auction_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4AF2B3F3A76ED395 ON bid (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C7440455E7927C74 ON client (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, image_url VARCHAR(255) NOT NULL, market_price_cents INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, pack_id INTEGER NOT NULL, total_cents INTEGER NOT NULL, paid_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , status VARCHAR(10) NOT NULL, CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6117D13B1919B217 FOREIGN KEY (pack_id) REFERENCES token_pack (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13BA76ED395 ON purchase (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6117D13B1919B217 ON purchase (pack_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE token_pack (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(100) NOT NULL, tokens INTEGER NOT NULL, price_cents INTEGER NOT NULL, active BOOLEAN NOT NULL, name VARCHAR(100) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "transaction" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, wallet_id INTEGER NOT NULL, type VARCHAR(10) NOT NULL, amount INTEGER NOT NULL, reference_id INTEGER DEFAULT NULL, created_id INTEGER NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_723705D1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_723705D1712520F3 ON "transaction" (wallet_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
            , password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wallet (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, balance DOUBLE PRECISION NOT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_7C68921FA76ED395 FOREIGN KEY (user_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7C68921FA76ED395 ON wallet (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE auction
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bid
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE product
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE token_pack
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "transaction"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wallet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
