<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227173825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE inscription (id INT AUTO_INCREMENT NOT NULL, sortie_id INT NOT NULL, participant_id INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_74E0281CCC72D953 (sortie_id), INDEX IDX_74E0281C9D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_74E0281CCC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id)');
        //$this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_74E0281C9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        //$this->addSql('DROP INDEX IDX_3C3FD3F29C026F8E ON sortie');
        //$this->addSql('ALTER TABLE sortie CHANGE campus_organisateur_id campus_id INT NOT NULL');
        //$this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        //$this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F217AE6A42 FOREIGN KEY (participant_organisateur_id) REFERENCES participant (id)');
        //$this->addSql('CREATE INDEX IDX_3C3FD3F2AF5D55E1 ON sortie (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_74E0281CCC72D953');
        $this->addSql('ALTER TABLE inscription DROP FOREIGN KEY FK_74E0281C9D1C3019');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2AF5D55E1');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F217AE6A42');
        $this->addSql('DROP INDEX IDX_3C3FD3F2AF5D55E1 ON sortie');
        $this->addSql('ALTER TABLE sortie CHANGE campus_id campus_organisateur_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_3C3FD3F29C026F8E ON sortie (campus_organisateur_id)');
    }
}
