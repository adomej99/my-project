<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230422130130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE return_report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, returned_by_id INT NOT NULL, report VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, INDEX IDX_80F2ED5BA76ED395 (user_id), INDEX IDX_80F2ED5B71AD87D9 (returned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE return_report ADD CONSTRAINT FK_80F2ED5BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE return_report ADD CONSTRAINT FK_80F2ED5B71AD87D9 FOREIGN KEY (returned_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD is_active INT NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE return_report DROP FOREIGN KEY FK_80F2ED5BA76ED395');
        $this->addSql('ALTER TABLE return_report DROP FOREIGN KEY FK_80F2ED5B71AD87D9');
        $this->addSql('DROP TABLE return_report');
        $this->addSql('ALTER TABLE user DROP is_active');
    }
}
