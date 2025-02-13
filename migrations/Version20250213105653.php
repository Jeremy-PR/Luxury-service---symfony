<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213105653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_offer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, user VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE job_offer_type');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1CF9A1456');
        $this->addSql('DROP INDEX IDX_64C19C1CF9A1456 ON category');
        $this->addSql('ALTER TABLE category CHANGE job_offer_type_id job_offer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C13481D195 FOREIGN KEY (job_offer_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_64C19C13481D195 ON category (job_offer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_offer_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE job_offer');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C13481D195');
        $this->addSql('DROP INDEX IDX_64C19C13481D195 ON category');
        $this->addSql('ALTER TABLE category CHANGE job_offer_id job_offer_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1CF9A1456 FOREIGN KEY (job_offer_type_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64C19C1CF9A1456 ON category (job_offer_type_id)');
    }
}
