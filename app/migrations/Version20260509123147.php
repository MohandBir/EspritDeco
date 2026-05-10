<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260509123147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(40) NOT NULL, user_id INT NOT NULL, INDEX IDX_BA388B7A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cart_line (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_3EF1B4CF1AD5CDBF (cart_id), INDEX IDX_3EF1B4CF4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, user_id INT NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, unit_price DOUBLE PRECISION NOT NULL, customer_order_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_9CE58EE1A15A2E17 (customer_order_id), INDEX IDX_9CE58EE14584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_line ADD CONSTRAINT FK_3EF1B4CF1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_line ADD CONSTRAINT FK_3EF1B4CF4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1A15A2E17 FOREIGN KEY (customer_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart_line DROP FOREIGN KEY FK_3EF1B4CF1AD5CDBF');
        $this->addSql('ALTER TABLE cart_line DROP FOREIGN KEY FK_3EF1B4CF4584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1A15A2E17');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_line');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_line');
    }
}
