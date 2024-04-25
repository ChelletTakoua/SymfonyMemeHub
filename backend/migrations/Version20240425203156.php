<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425203156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4A7B9261D');
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4ADF6E65AD');
        $this->addSql('DROP INDEX IDX_B782BE4ADF6E65AD ON blocked_meme');
        $this->addSql('DROP INDEX UNIQ_B782BE4A7B9261D ON blocked_meme');
        $this->addSql('ALTER TABLE blocked_meme ADD admin_id INT NOT NULL, ADD meme_id INT NOT NULL, DROP admin_id_id, DROP meme_id_id');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4A642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4ADB6EC45D FOREIGN KEY (meme_id) REFERENCES meme (id)');
        $this->addSql('CREATE INDEX IDX_B782BE4A642B8210 ON blocked_meme (admin_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B782BE4ADB6EC45D ON blocked_meme (meme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4A642B8210');
        $this->addSql('ALTER TABLE blocked_meme DROP FOREIGN KEY FK_B782BE4ADB6EC45D');
        $this->addSql('DROP INDEX IDX_B782BE4A642B8210 ON blocked_meme');
        $this->addSql('DROP INDEX UNIQ_B782BE4ADB6EC45D ON blocked_meme');
        $this->addSql('ALTER TABLE blocked_meme ADD admin_id_id INT NOT NULL, ADD meme_id_id INT NOT NULL, DROP admin_id, DROP meme_id');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4A7B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
        $this->addSql('ALTER TABLE blocked_meme ADD CONSTRAINT FK_B782BE4ADF6E65AD FOREIGN KEY (admin_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B782BE4ADF6E65AD ON blocked_meme (admin_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B782BE4A7B9261D ON blocked_meme (meme_id_id)');
    }
}
