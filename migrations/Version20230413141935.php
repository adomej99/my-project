<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413141935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_history (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, performed_by_id INT NOT NULL, date_created VARCHAR(255) NOT NULL, action INT NOT NULL, is_request TINYINT(1) NOT NULL, INDEX IDX_B49A58DD16A2B381 (book_id), INDEX IDX_B49A58DD2E65C292 (performed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_history ADD CONSTRAINT FK_B49A58DD16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_history ADD CONSTRAINT FK_B49A58DD2E65C292 FOREIGN KEY (performed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_history DROP FOREIGN KEY FK_B49A58DD16A2B381');
        $this->addSql('ALTER TABLE book_history DROP FOREIGN KEY FK_B49A58DD2E65C292');
        $this->addSql('DROP TABLE book_history');
    }
}
