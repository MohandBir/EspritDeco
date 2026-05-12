<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511133613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adress (id INT AUTO_INCREMENT NOT NULL, fullname VARCHAR(100) NOT NULL, street VARCHAR(255) NOT NULL, zip_code INT NOT NULL, city VARCHAR(50) NOT NULL, customer_order_id INT NOT NULL, UNIQUE INDEX UNIQ_5CECC7BEA15A2E17 (customer_order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE adress ADD CONSTRAINT FK_5CECC7BEA15A2E17 FOREIGN KEY (customer_order_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adress DROP FOREIGN KEY FK_5CECC7BEA15A2E17');
        $this->addSql('DROP TABLE adress');
    }
}
