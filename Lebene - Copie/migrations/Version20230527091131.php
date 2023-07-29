<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230527091131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, numero_entreprise_check TINYINT(1) NOT NULL, email_entreprise_check TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, gerant_id INT DEFAULT NULL, administrateur_id INT DEFAULT NULL, zip VARCHAR(255) NOT NULL, indication VARCHAR(255) NOT NULL, roles JSON NOT NULL, statut VARCHAR(255) NOT NULL, code_ui VARCHAR(255) NOT NULL, INDEX IDX_C7440455A500A924 (gerant_id), INDEX IDX_C74404557EE5403C (administrateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE code_ui_all (id INT AUTO_INCREMENT NOT NULL, code_ui VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, gerant_id INT DEFAULT NULL, type_depense INT NOT NULL, nom_produit VARCHAR(60) NOT NULL, description VARCHAR(255) NOT NULL, prix_total INT NOT NULL, calcul_depense TINYINT(1) NOT NULL, created_at VARCHAR(60) NOT NULL, INDEX IDX_34059757642B8210 (admin_id), INDEX IDX_34059757A500A924 (gerant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT NOT NULL, gerant_id INT DEFAULT NULL, avatar VARCHAR(60) DEFAULT NULL, salaire INT NOT NULL, anne_experience INT DEFAULT NULL, type_paiement TINYINT(1) NOT NULL, roles JSON NOT NULL, code_ui VARCHAR(255) NOT NULL, INDEX IDX_F804D3B97EE5403C (administrateur_id), INDEX IDX_F804D3B9A500A924 (gerant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe_equipe (id INT AUTO_INCREMENT NOT NULL, employe_id INT NOT NULL, equipe_id INT DEFAULT NULL, INDEX IDX_4A6FA1EC1B65292 (employe_id), INDEX IDX_4A6FA1EC6D861B89 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entete (id INT AUTO_INCREMENT NOT NULL, facture_id INT DEFAULT NULL, tarifs_id INT NOT NULL, quantite INT NOT NULL, prix_total INT NOT NULL, express TINYINT(1) NOT NULL, date_delivered_express VARCHAR(255) DEFAULT NULL, express_delivered TINYINT(1) NOT NULL, INDEX IDX_1333E1437F2DEE08 (facture_id), INDEX IDX_1333E143F5F3287F (tarifs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT NOT NULL, numero_tel_entre INT NOT NULL, email_entre VARCHAR(255) NOT NULL, numero_entreprise_check TINYINT(1) DEFAULT NULL, email_entreprise_check TINYINT(1) NOT NULL, nom VARCHAR(60) NOT NULL, adresse VARCHAR(255) NOT NULL, zip VARCHAR(255) NOT NULL, INDEX IDX_D19FA607EE5403C (administrateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, administrateur_id INT DEFAULT NULL, gerant_id INT DEFAULT NULL, nom VARCHAR(60) NOT NULL, avatar VARCHAR(60) DEFAULT NULL, created_at VARCHAR(60) NOT NULL, description VARCHAR(255) DEFAULT NULL, code_ui VARCHAR(255) NOT NULL, INDEX IDX_2449BA157EE5403C (administrateur_id), INDEX IDX_2449BA15A500A924 (gerant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, admin_id INT NOT NULL, client_id INT NOT NULL, livraison_id INT DEFAULT NULL, total_ttc INT NOT NULL, total_tva INT NOT NULL, taux_reduction DOUBLE PRECISION DEFAULT NULL, date_livraison VARCHAR(255) NOT NULL, num_facture INT DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, facture_id_number VARCHAR(255) NOT NULL, express TINYINT(1) NOT NULL, delivered_at VARCHAR(255) DEFAULT NULL, date_recuperation VARCHAR(255) NOT NULL, invoice_code VARCHAR(255) NOT NULL, jours_passer INT DEFAULT NULL, heure_passer INT DEFAULT NULL, INDEX IDX_FE866410642B8210 (admin_id), INDEX IDX_FE86641019EB6921 (client_id), INDEX IDX_FE8664108E54FB25 (livraison_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture_equipe (id INT AUTO_INCREMENT NOT NULL, facture_id INT NOT NULL, equipe_id INT NOT NULL, INDEX IDX_71DEA8B27F2DEE08 (facture_id), INDEX IDX_71DEA8B26D861B89 (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gerant (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, code_ui VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE icons (id INT AUTO_INCREMENT NOT NULL, syntaxe_icon VARCHAR(255) DEFAULT NULL, nom_icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, date_recup VARCHAR(255) NOT NULL, date_livr VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, express TINYINT(1) NOT NULL, delivred_at VARCHAR(255) DEFAULT NULL, INDEX IDX_A60C9F1F19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, gerant_id INT DEFAULT NULL, client_id INT DEFAULT NULL, facture_id INT DEFAULT NULL, depense_id INT DEFAULT NULL, employe_id INT DEFAULT NULL, livraison_id INT DEFAULT NULL, equipe_id INT DEFAULT NULL, employe_equipe_id INT DEFAULT NULL, paiement_id INT DEFAULT NULL, titre VARCHAR(60) NOT NULL, reader TINYINT(1) NOT NULL, created_at VARCHAR(255) NOT NULL, type_notif VARCHAR(60) NOT NULL, INDEX IDX_6000B0D3642B8210 (admin_id), INDEX IDX_6000B0D3A500A924 (gerant_id), INDEX IDX_6000B0D319EB6921 (client_id), INDEX IDX_6000B0D37F2DEE08 (facture_id), INDEX IDX_6000B0D341D81563 (depense_id), INDEX IDX_6000B0D31B65292 (employe_id), INDEX IDX_6000B0D38E54FB25 (livraison_id), INDEX IDX_6000B0D36D861B89 (equipe_id), INDEX IDX_6000B0D3ACA81DDF (employe_equipe_id), INDEX IDX_6000B0D32A4C4478 (paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, employe_id INT DEFAULT NULL, administrateur_id INT DEFAULT NULL, gerant_id INT DEFAULT NULL, paiement_at VARCHAR(255) NOT NULL, moyen_paiement VARCHAR(60) NOT NULL, augmentation INT DEFAULT NULL, paiement_final INT NOT NULL, INDEX IDX_B1DC7A1E1B65292 (employe_id), INDEX IDX_B1DC7A1E7EE5403C (administrateur_id), INDEX IDX_B1DC7A1EA500A924 (gerant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rappel (id INT AUTO_INCREMENT NOT NULL, created_by_admin_id INT DEFAULT NULL, created_by_gerant_id INT DEFAULT NULL, facture_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(300) DEFAULT NULL, jour_at VARCHAR(255) DEFAULT NULL, heure_at VARCHAR(255) DEFAULT NULL, all_day TINYINT(1) DEFAULT NULL, created_at VARCHAR(255) NOT NULL, type_rappel INT NOT NULL, date_fin_at VARCHAR(255) DEFAULT NULL, heure_fin_at VARCHAR(255) DEFAULT NULL, INDEX IDX_303A29C964F1F4EE (created_by_admin_id), INDEX IDX_303A29C9FF05AC49 (created_by_gerant_id), INDEX IDX_303A29C97F2DEE08 (facture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarifs (id INT AUTO_INCREMENT NOT NULL, admin_id INT DEFAULT NULL, icons_id INT DEFAULT NULL, prix INT NOT NULL, nombre INT DEFAULT NULL, type INT NOT NULL, express TINYINT(1) DEFAULT NULL, INDEX IDX_F9B8C496642B8210 (admin_id), INDEX IDX_F9B8C4962FF25572 (icons_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_lavage (id INT AUTO_INCREMENT NOT NULL, nom_type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_notif (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(60) NOT NULL, nom_type LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_rappel (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, gerant_id INT DEFAULT NULL, administrateur_id INT DEFAULT NULL, employe_id INT DEFAULT NULL, created_by_gerant_id INT DEFAULT NULL, created_by_admin_id INT DEFAULT NULL, photo_profile VARCHAR(255) DEFAULT NULL, nom VARCHAR(30) NOT NULL, numero INT NOT NULL, email VARCHAR(60) DEFAULT NULL, email_check TINYINT(1) NOT NULL, numero_check TINYINT(1) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, username VARCHAR(30) NOT NULL, created_at VARCHAR(30) NOT NULL, roles JSON NOT NULL, adresse VARCHAR(255) NOT NULL, etat_suspendu TINYINT(1) DEFAULT NULL, sexe VARCHAR(60) DEFAULT NULL, INDEX IDX_1D1C63B319EB6921 (client_id), INDEX IDX_1D1C63B3A500A924 (gerant_id), INDEX IDX_1D1C63B37EE5403C (administrateur_id), INDEX IDX_1D1C63B31B65292 (employe_id), INDEX IDX_1D1C63B3FF05AC49 (created_by_gerant_id), INDEX IDX_1D1C63B364F1F4EE (created_by_admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404557EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B97EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE employe_equipe ADD CONSTRAINT FK_4A6FA1EC1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE employe_equipe ADD CONSTRAINT FK_4A6FA1EC6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE entete ADD CONSTRAINT FK_1333E1437F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE entete ADD CONSTRAINT FK_1333E143F5F3287F FOREIGN KEY (tarifs_id) REFERENCES tarifs (id)');
        $this->addSql('ALTER TABLE entreprise ADD CONSTRAINT FK_D19FA607EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA157EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664108E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('ALTER TABLE facture_equipe ADD CONSTRAINT FK_71DEA8B27F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_equipe ADD CONSTRAINT FK_71DEA8B26D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE livraison ADD CONSTRAINT FK_A60C9F1F19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D37F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D341D81563 FOREIGN KEY (depense_id) REFERENCES depense (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D31B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D38E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D36D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3ACA81DDF FOREIGN KEY (employe_equipe_id) REFERENCES employe_equipe (id)');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D32A4C4478 FOREIGN KEY (paiement_id) REFERENCES paiement (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E7EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EA500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE rappel ADD CONSTRAINT FK_303A29C964F1F4EE FOREIGN KEY (created_by_admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE rappel ADD CONSTRAINT FK_303A29C9FF05AC49 FOREIGN KEY (created_by_gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE rappel ADD CONSTRAINT FK_303A29C97F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE tarifs ADD CONSTRAINT FK_F9B8C496642B8210 FOREIGN KEY (admin_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE tarifs ADD CONSTRAINT FK_F9B8C4962FF25572 FOREIGN KEY (icons_id) REFERENCES icons (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3A500A924 FOREIGN KEY (gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B37EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B31B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FF05AC49 FOREIGN KEY (created_by_gerant_id) REFERENCES gerant (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B364F1F4EE FOREIGN KEY (created_by_admin_id) REFERENCES administrateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404557EE5403C');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757642B8210');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B97EE5403C');
        $this->addSql('ALTER TABLE entreprise DROP FOREIGN KEY FK_D19FA607EE5403C');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA157EE5403C');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410642B8210');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3642B8210');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E7EE5403C');
        $this->addSql('ALTER TABLE rappel DROP FOREIGN KEY FK_303A29C964F1F4EE');
        $this->addSql('ALTER TABLE tarifs DROP FOREIGN KEY FK_F9B8C496642B8210');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B37EE5403C');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B364F1F4EE');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE livraison DROP FOREIGN KEY FK_A60C9F1F19EB6921');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D319EB6921');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B319EB6921');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D341D81563');
        $this->addSql('ALTER TABLE employe_equipe DROP FOREIGN KEY FK_4A6FA1EC1B65292');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D31B65292');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E1B65292');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B31B65292');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3ACA81DDF');
        $this->addSql('ALTER TABLE employe_equipe DROP FOREIGN KEY FK_4A6FA1EC6D861B89');
        $this->addSql('ALTER TABLE facture_equipe DROP FOREIGN KEY FK_71DEA8B26D861B89');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D36D861B89');
        $this->addSql('ALTER TABLE entete DROP FOREIGN KEY FK_1333E1437F2DEE08');
        $this->addSql('ALTER TABLE facture_equipe DROP FOREIGN KEY FK_71DEA8B27F2DEE08');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D37F2DEE08');
        $this->addSql('ALTER TABLE rappel DROP FOREIGN KEY FK_303A29C97F2DEE08');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A500A924');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757A500A924');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9A500A924');
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15A500A924');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A500A924');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EA500A924');
        $this->addSql('ALTER TABLE rappel DROP FOREIGN KEY FK_303A29C9FF05AC49');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3A500A924');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FF05AC49');
        $this->addSql('ALTER TABLE tarifs DROP FOREIGN KEY FK_F9B8C4962FF25572');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664108E54FB25');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D38E54FB25');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D32A4C4478');
        $this->addSql('ALTER TABLE entete DROP FOREIGN KEY FK_1333E143F5F3287F');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE code_ui_all');
        $this->addSql('DROP TABLE depense');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE employe_equipe');
        $this->addSql('DROP TABLE entete');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE facture_equipe');
        $this->addSql('DROP TABLE gerant');
        $this->addSql('DROP TABLE icons');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE rappel');
        $this->addSql('DROP TABLE tarifs');
        $this->addSql('DROP TABLE type_lavage');
        $this->addSql('DROP TABLE type_notif');
        $this->addSql('DROP TABLE type_rappel');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
