<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425204152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE text_block DROP FOREIGN KEY FK_D5AF2D7F7B9261D');
        $this->addSql('DROP INDEX IDX_D5AF2D7F7B9261D ON text_block');
        $this->addSql('ALTER TABLE text_block CHANGE meme_id_id meme_id INT NOT NULL');
        $this->addSql('ALTER TABLE text_block ADD CONSTRAINT FK_D5AF2D7FDB6EC45D FOREIGN KEY (meme_id) REFERENCES meme (id)');
        $this->addSql('CREATE INDEX IDX_D5AF2D7FDB6EC45D ON text_block (meme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE text_block DROP FOREIGN KEY FK_D5AF2D7FDB6EC45D');
        $this->addSql('DROP INDEX IDX_D5AF2D7FDB6EC45D ON text_block');
        $this->addSql('ALTER TABLE text_block CHANGE meme_id meme_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE text_block ADD CONSTRAINT FK_D5AF2D7F7B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
        $this->addSql('CREATE INDEX IDX_D5AF2D7F7B9261D ON text_block (meme_id_id)');
    }
}
