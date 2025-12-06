<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251201214217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Consolidated initial schema: design, shop, design_shop
        $this->addSql('CREATE TABLE design (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, picture VARCHAR(4) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(255) NOT NULL, logo VARCHAR(4) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE design_shop (id INT AUTO_INCREMENT NOT NULL, design_id INT NOT NULL, shop_id INT NOT NULL, published_at DATETIME DEFAULT NULL, last_update_at DATETIME DEFAULT NULL, INDEX IDX_DESIGN (design_id), INDEX IDX_SHOP (shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE design_shop ADD CONSTRAINT FK_DESIGN_SHOP_DESIGN FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE design_shop ADD CONSTRAINT FK_DESIGN_SHOP_SHOP FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // Drop in reverse order to avoid FK constraint issues
        $this->addSql('ALTER TABLE design_shop DROP FOREIGN KEY FK_DESIGN_SHOP_DESIGN');
        $this->addSql('ALTER TABLE design_shop DROP FOREIGN KEY FK_DESIGN_SHOP_SHOP');
        $this->addSql('DROP TABLE design_shop');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE design');
    }
}
