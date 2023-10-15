<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231009101901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @return void
     * @todo Migration für User-Entity ist verloren gegangen - beheben
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE hyperparameters_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE layer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE model_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hyperparameters (id INT NOT NULL, model_id INT NOT NULL, gradient_strategy VARCHAR(50) DEFAULT NULL, batchsize INT DEFAULT NULL, epochs INT NOT NULL, early_stop BOOLEAN NOT NULL, early_stop_treshold INT NOT NULL, early_stop_function VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_570F6AA07975B7E7 ON hyperparameters (model_id)');
        $this->addSql('CREATE TABLE layer (id INT NOT NULL, model_id INT NOT NULL, type VARCHAR(255) NOT NULL, neuron_count INT NOT NULL, activation_function VARCHAR(255) NOT NULL, dropout_quote INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E4DB211A7975B7E7 ON layer (model_id)');
        $this->addSql('CREATE TABLE model (id INT NOT NULL, name VARCHAR(500) NOT NULL, lookup VARCHAR(255) NOT NULL, scaler VARCHAR(255) DEFAULT NULL, creationdate TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE hyperparameters ADD CONSTRAINT FK_570F6AA07975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE layer ADD CONSTRAINT FK_E4DB211A7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE hyperparameters_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE layer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE model_id_seq CASCADE');
        $this->addSql('ALTER TABLE hyperparameters DROP CONSTRAINT FK_570F6AA07975B7E7');
        $this->addSql('ALTER TABLE layer DROP CONSTRAINT FK_E4DB211A7975B7E7');
        $this->addSql('DROP TABLE hyperparameters');
        $this->addSql('DROP TABLE layer');
        $this->addSql('DROP TABLE model');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
