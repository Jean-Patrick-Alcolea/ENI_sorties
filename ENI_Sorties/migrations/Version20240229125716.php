<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229125716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE filtre (id INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, choix_id_campus INT DEFAULT NULL, string_mot_search VARCHAR(255) DEFAULT NULL, date_debut_search DATETIME DEFAULT NULL, date_fin_search DATETIME DEFAULT NULL, check_user_organise TINYINT(1) NOT NULL, check_user_inscrit TINYINT(1) NOT NULL, check_sortie_passee TINYINT(1) NOT NULL, date_filtre DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('ALTER TABLE lieu ADD CONSTRAINT FK_2F577D59A73F0036 FOREIGN KEY (ville_id) REFERENCES ville (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP TABLE filtre');
        //$this->addSql('ALTER TABLE lieu DROP FOREIGN KEY FK_2F577D59A73F0036');
    }
}
