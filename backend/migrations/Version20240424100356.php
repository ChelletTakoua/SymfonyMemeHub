<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424100356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meme (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, template_id_id INT NOT NULL, title VARCHAR(100) DEFAULT NULL, creation_date DATETIME NOT NULL, num_likes INT NOT NULL, result_img LONGBLOB NOT NULL, INDEX IDX_4B9F79349D86650F (user_id_id), INDEX IDX_4B9F79344C924D98 (template_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F79349D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F79344C924D98 FOREIGN KEY (template_id_id) REFERENCES template (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F79349D86650F');
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F79344C924D98');
        $this->addSql('DROP TABLE meme');
    }
}
