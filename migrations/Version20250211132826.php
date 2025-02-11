<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250211132826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate ADD expe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44CFB9C84C FOREIGN KEY (expe_id) REFERENCES expe (id)');
        $this->addSql('CREATE INDEX IDX_C8B28E44CFB9C84C ON candidate (expe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44CFB9C84C');
        $this->addSql('DROP INDEX IDX_C8B28E44CFB9C84C ON candidate');
        $this->addSql('ALTER TABLE candidate DROP expe_id');
    }
}
