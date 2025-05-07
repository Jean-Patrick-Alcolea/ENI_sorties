<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227163440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD campus_organisateur_id INT NOT NULL, ADD participant_organisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F29C026F8E FOREIGN KEY (campus_organisateur_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F217AE6A42 FOREIGN KEY (participant_organisateur_id) REFERENCES participant (id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F29C026F8E ON sortie (campus_organisateur_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F217AE6A42 ON sortie (participant_organisateur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F29C026F8E');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F217AE6A42');
        $this->addSql('DROP INDEX IDX_3C3FD3F29C026F8E ON sortie');
        $this->addSql('DROP INDEX IDX_3C3FD3F217AE6A42 ON sortie');
        $this->addSql('ALTER TABLE sortie DROP campus_organisateur_id, DROP participant_organisateur_id');
    }
}
