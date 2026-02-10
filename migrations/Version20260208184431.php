<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208184431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prediction DROP FOREIGN KEY `FK_36396FC8A76ED395`');
        $this->addSql('DROP INDEX IDX_36396FC8A76ED395 ON prediction');
        $this->addSql('ALTER TABLE prediction ADD confiance_ai DOUBLE PRECISION NOT NULL, DROP user_id, CHANGE score_predit vainqueur_predi VARCHAR(255) NOT NULL, CHANGE date_prediction created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prediction ADD user_id INT NOT NULL, DROP confiance_ai, CHANGE vainqueur_predi score_predit VARCHAR(255) NOT NULL, CHANGE created_at date_prediction DATETIME NOT NULL');
        $this->addSql('ALTER TABLE prediction ADD CONSTRAINT `FK_36396FC8A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_36396FC8A76ED395 ON prediction (user_id)');
    }
}
