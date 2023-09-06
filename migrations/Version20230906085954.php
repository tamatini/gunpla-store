<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906085954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE252745F80CD');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE252745F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE252745F80CD');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE252745F80CD FOREIGN KEY (shopping_cart_id) REFERENCES shopping_cart (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
