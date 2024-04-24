<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424114400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blocked_meme (id INT AUTO_INCREMENT NOT NULL, admin_id_id INT NOT NULL, meme_id_id INT NOT NULL, INDEX IDX_B782BE4ADF6E65AD (admin_id_id), UNIQUE INDEX UNIQ_B782BE4A7B9261D (meme_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4ADF6E65AD FOREIGN KEY (admin_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4A7B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
        $this->addSql('ALTER TABLE report ADD blocked_meme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778462BCACAB FOREIGN KEY (blocked_meme_id) REFERENCES blocked_meme (id)');
        $this->addSql('CREATE INDEX IDX_C42F778462BCACAB ON report (blocked_meme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F778462BCACAB');
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4ADF6E65AD');
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4A7B9261D');
        $this->addSql('DROP TABLE blocked_meme');
        $this->addSql('DROP INDEX IDX_C42F778462BCACAB ON report');
        $this->addSql('ALTER TABLE report DROP blocked_meme_id');
    }
}
