<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231103145435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE training_task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE training_task (id INT NOT NULL, model_id INT NOT NULL, script_path VARCHAR(1000) NOT NULL, report_path VARCHAR(1000) NOT NULL, state VARCHAR(20) NOT NULL, creation_datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, start_datetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, end_datetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, encoded_model TEXT DEFAULT NULL, model_hash VARCHAR(2000) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5DE49C667975B7E7 ON training_task (model_id)');
        $this->addSql('ALTER TABLE training_task ADD CONSTRAINT FK_5DE49C667975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE training_task_id_seq CASCADE');
        $this->addSql('ALTER TABLE training_task DROP CONSTRAINT FK_5DE49C667975B7E7');
        $this->addSql('DROP TABLE training_task');
    }
}
