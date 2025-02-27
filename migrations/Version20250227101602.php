<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227101602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date_commande DATETIME NOT NULL, fournisseur VARCHAR(255) NOT NULL, total_prix DOUBLE PRECISION NOT NULL, status VARCHAR(255) DEFAULT \'pending\' NOT NULL, stripe_session_id VARCHAR(255) DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande_medicament (commande_id INT NOT NULL, medicament_id INT NOT NULL, INDEX IDX_25E5EDC82EA2E54 (commande_id), INDEX IDX_25E5EDCAB0D61F7 (medicament_id), PRIMARY KEY(commande_id, medicament_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicament (id INT AUTO_INCREMENT NOT NULL, nom_medicament VARCHAR(255) NOT NULL, description_medicament VARCHAR(255) NOT NULL, type_medicament VARCHAR(255) NOT NULL, prix_medicament DOUBLE PRECISION NOT NULL, quantite_stock INT NOT NULL, date_entree DATETIME NOT NULL, date_expiration DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medicament_commande (commande_id INT NOT NULL, medicament_id INT NOT NULL, quantite INT NOT NULL, INDEX IDX_81D516D182EA2E54 (commande_id), INDEX IDX_81D516D1AB0D61F7 (medicament_id), PRIMARY KEY(commande_id, medicament_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_medicament ADD CONSTRAINT FK_25E5EDC82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande_medicament ADD CONSTRAINT FK_25E5EDCAB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE medicament_commande ADD CONSTRAINT FK_81D516D182EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE medicament_commande ADD CONSTRAINT FK_81D516D1AB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande_medicament DROP FOREIGN KEY FK_25E5EDC82EA2E54');
        $this->addSql('ALTER TABLE commande_medicament DROP FOREIGN KEY FK_25E5EDCAB0D61F7');
        $this->addSql('ALTER TABLE medicament_commande DROP FOREIGN KEY FK_81D516D182EA2E54');
        $this->addSql('ALTER TABLE medicament_commande DROP FOREIGN KEY FK_81D516D1AB0D61F7');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commande_medicament');
        $this->addSql('DROP TABLE medicament');
        $this->addSql('DROP TABLE medicament_commande');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
