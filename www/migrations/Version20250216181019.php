<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216181019 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955514103CF');
        $this->addSql('DROP INDEX IDX_42C84955514103CF ON reservation');
        $this->addSql('ALTER TABLE reservation ADD applied_price_total DOUBLE PRECISION NOT NULL, DROP applied_price_id, CHANGE status status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD applied_price_id INT NOT NULL, DROP applied_price_total, CHANGE status status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955514103CF FOREIGN KEY (applied_price_id) REFERENCES price (id)');
        $this->addSql('CREATE INDEX IDX_42C84955514103CF ON reservation (applied_price_id)');
    }
}
