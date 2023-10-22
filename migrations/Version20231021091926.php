<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231021091926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, executed_by_id INT NOT NULL, executed_on_id INT NOT NULL, type VARCHAR(255) NOT NULL, executiondate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, content JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA78B35AB5C ON event (executed_by_id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7BE6B00CB ON event (executed_on_id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA78B35AB5C FOREIGN KEY (executed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BE6B00CB FOREIGN KEY (executed_on_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA78B35AB5C');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA7BE6B00CB');
        $this->addSql('DROP TABLE event');
    }
}
