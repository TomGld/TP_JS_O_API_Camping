<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210131449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price (id INT AUTO_INCREMENT NOT NULL, rental_id INT NOT NULL, season_id INT NOT NULL, price_per_night INT NOT NULL, INDEX IDX_CAC822D9A7CF2329 (rental_id), UNIQUE INDEX UNIQ_CAC822D94EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rental (id INT AUTO_INCREMENT NOT NULL, type_rental_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, capacity INT NOT NULL, nbr_localization INT NOT NULL, is_active TINYINT(1) NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_1619C27D13096239 (type_rental_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rental_equipment (rental_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_6CD8D28CA7CF2329 (rental_id), INDEX IDX_6CD8D28C517FE9FE (equipment_id), PRIMARY KEY(rental_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, rental_id INT NOT NULL, renter_id INT NOT NULL, applied_price_id INT NOT NULL, date_start DATETIME NOT NULL, date_end DATETIME NOT NULL, nbr_adult INT NOT NULL, nbr_minor INT NOT NULL, status VARCHAR(255) NOT NULL, checked INT NOT NULL, INDEX IDX_42C84955A7CF2329 (rental_id), INDEX IDX_42C84955E289A545 (renter_id), INDEX IDX_42C84955514103CF (applied_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, season_start DATETIME NOT NULL, season_end DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_rental (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, date_of_birth DATETIME NOT NULL, username VARCHAR(255) NOT NULL, phone VARCHAR(15) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D9A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id)');
        $this->addSql('ALTER TABLE price ADD CONSTRAINT FK_CAC822D94EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE rental ADD CONSTRAINT FK_1619C27D13096239 FOREIGN KEY (type_rental_id) REFERENCES type_rental (id)');
        $this->addSql('ALTER TABLE rental_equipment ADD CONSTRAINT FK_6CD8D28CA7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rental_equipment ADD CONSTRAINT FK_6CD8D28C517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E289A545 FOREIGN KEY (renter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955514103CF FOREIGN KEY (applied_price_id) REFERENCES price (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D9A7CF2329');
        $this->addSql('ALTER TABLE price DROP FOREIGN KEY FK_CAC822D94EC001D1');
        $this->addSql('ALTER TABLE rental DROP FOREIGN KEY FK_1619C27D13096239');
        $this->addSql('ALTER TABLE rental_equipment DROP FOREIGN KEY FK_6CD8D28CA7CF2329');
        $this->addSql('ALTER TABLE rental_equipment DROP FOREIGN KEY FK_6CD8D28C517FE9FE');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A7CF2329');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E289A545');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955514103CF');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE price');
        $this->addSql('DROP TABLE rental');
        $this->addSql('DROP TABLE rental_equipment');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE type_rental');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
