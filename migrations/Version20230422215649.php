<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422215649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE return_report ADD request_id INT NOT NULL');
        $this->addSql('ALTER TABLE return_report ADD CONSTRAINT FK_80F2ED5B427EB8A5 FOREIGN KEY (request_id) REFERENCES book_request (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_80F2ED5B427EB8A5 ON return_report (request_id)');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE return_report DROP FOREIGN KEY FK_80F2ED5B427EB8A5');
        $this->addSql('DROP INDEX UNIQ_80F2ED5B427EB8A5 ON return_report');
        $this->addSql('ALTER TABLE return_report DROP request_id');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active INT DEFAULT 1 NOT NULL');
    }
}
