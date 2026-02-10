<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260209215511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prediction DROP created_at, CHANGE vainqueur_predi vainqueur_suggere VARCHAR(255) NOT NULL, CHANGE confiance_ai confiance DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prediction ADD created_at DATETIME NOT NULL, CHANGE vainqueur_suggere vainqueur_predi VARCHAR(255) NOT NULL, CHANGE confiance confiance_ai DOUBLE PRECISION NOT NULL');
    }
}
