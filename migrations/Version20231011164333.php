<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011164333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE layer ADD return_sequences BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE layer ADD regularization_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE layer ADD regularization_lambda INT DEFAULT NULL');
        $this->addSql('ALTER TABLE layer ADD recurrent_dropout_rate INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE layer DROP return_sequences');
        $this->addSql('ALTER TABLE layer DROP regularization_type');
        $this->addSql('ALTER TABLE layer DROP regularization_lambda');
        $this->addSql('ALTER TABLE layer DROP recurrent_dropout_rate');
    }
}
