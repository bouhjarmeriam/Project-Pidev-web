<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301220924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, etage_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, capacite INT NOT NULL, type_salle VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, priorite INT NOT NULL, INDEX IDX_4E977E5C984CE93F (etage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5C984CE93F FOREIGN KEY (etage_id) REFERENCES etage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5C984CE93F');
        $this->addSql('DROP TABLE salle');
    }
}
