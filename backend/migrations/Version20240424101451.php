<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424101451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE text_block (id INT AUTO_INCREMENT NOT NULL, meme_id_id INT NOT NULL, text VARCHAR(150) NOT NULL, x INT NOT NULL, y INT NOT NULL, font_size VARCHAR(10) NOT NULL, INDEX IDX_D5AF2D7F7B9261D (meme_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE text_block ADD CONSTRAINT FK_D5AF2D7F7B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE text_block DROP FOREIGN KEY FK_D5AF2D7F7B9261D');
        $this->addSql('DROP TABLE text_block');
    }
}
