<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230429131918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review ADD reviewed_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE book_review ADD CONSTRAINT FK_50948A4BFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50948A4BFC6B21F1 ON book_review (reviewed_by_id)');
        $this->addSql('ALTER TABLE user_review ADD reviewed_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_review ADD CONSTRAINT FK_1C119AFBFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1C119AFBFC6B21F1 ON user_review (reviewed_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_review DROP FOREIGN KEY FK_50948A4BFC6B21F1');
        $this->addSql('DROP INDEX IDX_50948A4BFC6B21F1 ON book_review');
        $this->addSql('ALTER TABLE book_review DROP reviewed_by_id');
        $this->addSql('ALTER TABLE user_review DROP FOREIGN KEY FK_1C119AFBFC6B21F1');
        $this->addSql('DROP INDEX IDX_1C119AFBFC6B21F1 ON user_review');
        $this->addSql('ALTER TABLE user_review DROP reviewed_by_id');
    }
}
