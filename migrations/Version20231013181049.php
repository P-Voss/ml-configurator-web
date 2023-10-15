<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013181049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE lin_reg_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE log_reg_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE svm_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE lin_reg_configuration (id INT NOT NULL, model_id INT NOT NULL, regularization_type VARCHAR(255) DEFAULT NULL, alpha INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AE9861DE7975B7E7 ON lin_reg_configuration (model_id)');
        $this->addSql('CREATE TABLE log_reg_configuration (id INT NOT NULL, model_id INT NOT NULL, regularizer_type VARCHAR(255) DEFAULT NULL, solver VARCHAR(255) NOT NULL, lambda INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4F861D67975B7E7 ON log_reg_configuration (model_id)');
        $this->addSql('CREATE TABLE svm_configuration (id INT NOT NULL, model_id INT NOT NULL, kernel VARCHAR(20) NOT NULL, c INT NOT NULL, degree INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_170F15667975B7E7 ON svm_configuration (model_id)');
        $this->addSql('ALTER TABLE lin_reg_configuration ADD CONSTRAINT FK_AE9861DE7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE log_reg_configuration ADD CONSTRAINT FK_F4F861D67975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE svm_configuration ADD CONSTRAINT FK_170F15667975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE lin_reg_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE log_reg_configuration_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE svm_configuration_id_seq CASCADE');
        $this->addSql('ALTER TABLE lin_reg_configuration DROP CONSTRAINT FK_AE9861DE7975B7E7');
        $this->addSql('ALTER TABLE log_reg_configuration DROP CONSTRAINT FK_F4F861D67975B7E7');
        $this->addSql('ALTER TABLE svm_configuration DROP CONSTRAINT FK_170F15667975B7E7');
        $this->addSql('DROP TABLE lin_reg_configuration');
        $this->addSql('DROP TABLE log_reg_configuration');
        $this->addSql('DROP TABLE svm_configuration');
    }
}
