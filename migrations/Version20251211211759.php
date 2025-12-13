<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211211759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE design (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, picture VARCHAR(4) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE design_shop (id INT AUTO_INCREMENT NOT NULL, published_at DATETIME DEFAULT NULL, last_update_at DATETIME DEFAULT NULL, design_id INT NOT NULL, shop_id INT NOT NULL, INDEX IDX_F14C0807E41DC9B2 (design_id), INDEX IDX_F14C08074D16C4DD (shop_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(255) NOT NULL, logo VARCHAR(4) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE design_shop ADD CONSTRAINT FK_F14C0807E41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE design_shop ADD CONSTRAINT FK_F14C08074D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE design_shop DROP FOREIGN KEY FK_F14C0807E41DC9B2');
        $this->addSql('ALTER TABLE design_shop DROP FOREIGN KEY FK_F14C08074D16C4DD');
        $this->addSql('DROP TABLE design');
        $this->addSql('DROP TABLE design_shop');
        $this->addSql('DROP TABLE shop');
        $this->addSql('DROP TABLE user');
    }
}
