<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250225211833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD status VARCHAR(255) DEFAULT \'pending\' NOT NULL, ADD stripe_session_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE medicament CHANGE description_medicament description_medicament VARCHAR(255) NOT NULL, CHANGE date_entree date_entree DATETIME NOT NULL, CHANGE date_expiration date_expiration DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP status, DROP stripe_session_id');
        $this->addSql('ALTER TABLE medicament CHANGE description_medicament description_medicament LONGTEXT NOT NULL, CHANGE date_entree date_entree DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE date_expiration date_expiration DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
