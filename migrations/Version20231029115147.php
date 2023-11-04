<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029115147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE hyperparameters_id_seq CASCADE');
        $this->addSql('ALTER TABLE hyperparameters DROP CONSTRAINT fk_570f6aa07975b7e7');
        $this->addSql('DROP TABLE hyperparameters');
        $this->addSql('ALTER TABLE "user" ADD is_demo_user BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD last_action_datetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE hyperparameters_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hyperparameters (id INT NOT NULL, model_id INT NOT NULL, gradient_strategy VARCHAR(50) DEFAULT NULL, batchsize INT DEFAULT NULL, epochs INT NOT NULL, early_stop BOOLEAN NOT NULL, early_stop_treshold INT NOT NULL, early_stop_function VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_570f6aa07975b7e7 ON hyperparameters (model_id)');
        $this->addSql('ALTER TABLE hyperparameters ADD CONSTRAINT fk_570f6aa07975b7e7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" DROP is_demo_user');
        $this->addSql('ALTER TABLE "user" DROP last_action_datetime');
    }
}
