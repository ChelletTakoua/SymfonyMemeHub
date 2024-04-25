<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425203713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B37B9261D');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B39D86650F');
        $this->addSql('DROP INDEX IDX_AC6340B39D86650F ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B37B9261D ON `like`');
        $this->addSql('ALTER TABLE `like` ADD user_id INT NOT NULL, ADD meme_id INT NOT NULL, DROP user_id_id, DROP meme_id_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3DB6EC45D FOREIGN KEY (meme_id) REFERENCES meme (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3A76ED395 ON `like` (user_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3DB6EC45D ON `like` (meme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3A76ED395');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3DB6EC45D');
        $this->addSql('DROP INDEX IDX_AC6340B3A76ED395 ON `like`');
        $this->addSql('DROP INDEX IDX_AC6340B3DB6EC45D ON `like`');
        $this->addSql('ALTER TABLE `like` ADD user_id_id INT NOT NULL, ADD meme_id_id INT NOT NULL, DROP user_id, DROP meme_id');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B37B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B39D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B39D86650F ON `like` (user_id_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B37B9261D ON `like` (meme_id_id)');
    }
}
