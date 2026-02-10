<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205164340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi ADD nom VARCHAR(255) NOT NULL, ADD date_tournoi DATETIME NOT NULL, DROP titre, DROP date_debut, DROP date_fin, DROP recompense, DROP statut');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tournoi ADD date_fin DATETIME NOT NULL, ADD recompense VARCHAR(255) NOT NULL, ADD statut VARCHAR(50) NOT NULL, CHANGE nom titre VARCHAR(255) NOT NULL, CHANGE date_tournoi date_debut DATETIME NOT NULL');
    }
}
