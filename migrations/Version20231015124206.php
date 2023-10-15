<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231015124206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE upload_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE upload_file (id INT NOT NULL, model_id INT NOT NULL, filename VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, upload_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, entry_count INT NOT NULL, content BYTEA DEFAULT NULL, contains_header BOOLEAN NOT NULL, header VARCHAR(1000) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81BB1697975B7E7 ON upload_file (model_id)');
        $this->addSql('ALTER TABLE upload_file ADD CONSTRAINT FK_81BB1697975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE upload_file_id_seq CASCADE');
        $this->addSql('ALTER TABLE upload_file DROP CONSTRAINT FK_81BB1697975B7E7');
        $this->addSql('DROP TABLE upload_file');
    }
}
