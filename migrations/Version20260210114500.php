<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210114500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY `FK_98197A65296CD8AE`');
        $this->addSql('ALTER TABLE player ADD pseudo VARCHAR(100) NOT NULL, ADD first_name VARCHAR(100) NOT NULL, ADD last_name VARCHAR(100) NOT NULL, ADD role VARCHAR(50) DEFAULT NULL, ADD birth_date DATE DEFAULT NULL, ADD created_at DATETIME NOT NULL, DROP name, CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE team ADD logo VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65296CD8AE');
        $this->addSql('ALTER TABLE player ADD name VARCHAR(255) NOT NULL, DROP pseudo, DROP first_name, DROP last_name, DROP role, DROP birth_date, DROP created_at, CHANGE team_id team_id INT NOT NULL');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT `FK_98197A65296CD8AE` FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE team DROP logo, DROP created_at');
    }
}
