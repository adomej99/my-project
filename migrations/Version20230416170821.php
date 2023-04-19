<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230416170821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_request ADD is_return INT NOT NULL');
        $this->addSql('ALTER TABLE book_request ADD CONSTRAINT FK_A8B7A7094DA1E751 FOREIGN KEY (requested_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A8B7A7094DA1E751 ON book_request (requested_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_request DROP FOREIGN KEY FK_A8B7A7094DA1E751');
        $this->addSql('DROP INDEX IDX_A8B7A7094DA1E751 ON book_request');
        $this->addSql('ALTER TABLE book_request DROP is_return');
    }
}
