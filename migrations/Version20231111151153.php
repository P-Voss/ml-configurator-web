<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231111151153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE model ADD model_path VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE model ADD checkpoint_path VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE model ADD scaler_path VARCHAR(1000) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE model DROP model_path');
        $this->addSql('ALTER TABLE model DROP checkpoint_path');
        $this->addSql('ALTER TABLE model DROP scaler_path');
    }
}
