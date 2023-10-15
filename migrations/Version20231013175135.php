<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231013175135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE decisiontree_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE decisiontree_configuration (id INT NOT NULL, model_id INT NOT NULL, max_depth INT NOT NULL, min_sample_split INT NOT NULL, max_features INT NOT NULL, min_samples_leaf INT NOT NULL, missing_value_handling VARCHAR(255) NOT NULL, quality_measure VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB66737C7975B7E7 ON decisiontree_configuration (model_id)');
        $this->addSql('ALTER TABLE decisiontree_configuration ADD CONSTRAINT FK_BB66737C7975B7E7 FOREIGN KEY (model_id) REFERENCES model (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE decisiontree_configuration_id_seq CASCADE');
        $this->addSql('ALTER TABLE decisiontree_configuration DROP CONSTRAINT FK_BB66737C7975B7E7');
        $this->addSql('DROP TABLE decisiontree_configuration');
    }
}
