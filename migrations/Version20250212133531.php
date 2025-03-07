<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212133531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD job_offer_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1CF9A1456 FOREIGN KEY (job_offer_type_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1CF9A1456 ON category (job_offer_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1CF9A1456');
        $this->addSql('DROP INDEX IDX_64C19C1CF9A1456 ON category');
        $this->addSql('ALTER TABLE category DROP job_offer_type_id');
    }
}
