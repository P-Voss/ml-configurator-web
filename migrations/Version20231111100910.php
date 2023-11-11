<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231111100910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE field_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE field_configuration (id INT NOT NULL, model_id INT NOT NULL, type VARCHAR(255) NOT NULL, is_target BOOLEAN NOT NULL, is_ignored BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_39F3D18E7975B7E7 ON field_configuration (model_id)');
        $this->addSql('ALTER TABLE field_configuration ADD CONSTRAINT FK_39F3D18E7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE model ADD dataset VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE field_configuration_id_seq CASCADE');
        $this->addSql('ALTER TABLE field_configuration DROP CONSTRAINT FK_39F3D18E7975B7E7');
        $this->addSql('DROP TABLE field_configuration');
        $this->addSql('ALTER TABLE model DROP dataset');
    }
}
