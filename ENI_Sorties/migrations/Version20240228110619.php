<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228110619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('ALTER TABLE inscription RENAME INDEX idx_74e0281ccc72d953 TO IDX_5E90F6D6CC72D953');
        //$this->addSql('ALTER TABLE inscription RENAME INDEX idx_74e0281c9d1c3019 TO IDX_5E90F6D69D1C3019');
        //$this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F29C026F8E');
        //$this->addSql('DROP INDEX IDX_3C3FD3F29C026F8E ON sortie');
        //$this->addSql('ALTER TABLE sortie CHANGE campus_organisateur_id campus_id INT NOT NULL');
        //$this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        //$this->addSql('CREATE INDEX IDX_3C3FD3F2AF5D55E1 ON sortie (campus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription RENAME INDEX idx_5e90f6d6cc72d953 TO IDX_74E0281CCC72D953');
        $this->addSql('ALTER TABLE inscription RENAME INDEX idx_5e90f6d69d1c3019 TO IDX_74E0281C9D1C3019');
        $this->addSql('ALTER TABLE sortie DROP FOREIGN KEY FK_3C3FD3F2AF5D55E1');
        $this->addSql('DROP INDEX IDX_3C3FD3F2AF5D55E1 ON sortie');
        $this->addSql('ALTER TABLE sortie CHANGE campus_id campus_organisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F29C026F8E FOREIGN KEY (campus_organisateur_id) REFERENCES campus (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3C3FD3F29C026F8E ON sortie (campus_organisateur_id)');
    }
}
