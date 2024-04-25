<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425204011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77849D86650F');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77847B9261D');
        $this->addSql('DROP INDEX IDX_C42F77847B9261D ON report');
        $this->addSql('DROP INDEX IDX_C42F77849D86650F ON report');
        $this->addSql('ALTER TABLE report ADD meme_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, DROP meme_id_id, DROP user_id_id');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784DB6EC45D FOREIGN KEY (meme_id) REFERENCES meme (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C42F7784DB6EC45D ON report (meme_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784A76ED395 ON report (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784DB6EC45D');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784A76ED395');
        $this->addSql('DROP INDEX IDX_C42F7784DB6EC45D ON report');
        $this->addSql('DROP INDEX IDX_C42F7784A76ED395 ON report');
        $this->addSql('ALTER TABLE report ADD meme_id_id INT DEFAULT NULL, ADD user_id_id INT DEFAULT NULL, DROP meme_id, DROP user_id');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77849D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77847B9261D FOREIGN KEY (meme_id_id) REFERENCES meme (id)');
        $this->addSql('CREATE INDEX IDX_C42F77847B9261D ON report (meme_id_id)');
        $this->addSql('CREATE INDEX IDX_C42F77849D86650F ON report (user_id_id)');
    }
}
