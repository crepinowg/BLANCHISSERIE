<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622122327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cle_produit ADD administrateur_id INT DEFAULT NULL, ADD super_admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cle_produit ADD CONSTRAINT FK_4751B3F37EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE cle_produit ADD CONSTRAINT FK_4751B3F3BBF91D3B FOREIGN KEY (super_admin_id) REFERENCES super_admin (id)');
        $this->addSql('CREATE INDEX IDX_4751B3F37EE5403C ON cle_produit (administrateur_id)');
        $this->addSql('CREATE INDEX IDX_4751B3F3BBF91D3B ON cle_produit (super_admin_id)');
        $this->addSql('ALTER TABLE utilisateur ADD super_admin_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3BBF91D3B FOREIGN KEY (super_admin_id) REFERENCES super_admin (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3BBF91D3B ON utilisateur (super_admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cle_produit DROP FOREIGN KEY FK_4751B3F37EE5403C');
        $this->addSql('ALTER TABLE cle_produit DROP FOREIGN KEY FK_4751B3F3BBF91D3B');
        $this->addSql('DROP INDEX IDX_4751B3F37EE5403C ON cle_produit');
        $this->addSql('DROP INDEX IDX_4751B3F3BBF91D3B ON cle_produit');
        $this->addSql('ALTER TABLE cle_produit DROP administrateur_id, DROP super_admin_id');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3BBF91D3B');
        $this->addSql('DROP INDEX IDX_1D1C63B3BBF91D3B ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP super_admin_id');
    }
}
