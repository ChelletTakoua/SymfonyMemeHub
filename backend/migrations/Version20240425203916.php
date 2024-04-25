<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425203916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F79349D86650F');
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F79344C924D98');
        $this->addSql('DROP INDEX IDX_4B9F79349D86650F ON meme');
        $this->addSql('DROP INDEX IDX_4B9F79344C924D98 ON meme');
        $this->addSql('ALTER TABLE meme ADD user_id INT NOT NULL, ADD template_id INT NOT NULL, DROP user_id_id, DROP template_id_id');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F7934A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F79345DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
        $this->addSql('CREATE INDEX IDX_4B9F7934A76ED395 ON meme (user_id)');
        $this->addSql('CREATE INDEX IDX_4B9F79345DA0FB8 ON meme (template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F7934A76ED395');
        $this->addSql('ALTER TABLE meme DROP FOREIGN KEY FK_4B9F79345DA0FB8');
        $this->addSql('DROP INDEX IDX_4B9F7934A76ED395 ON meme');
        $this->addSql('DROP INDEX IDX_4B9F79345DA0FB8 ON meme');
        $this->addSql('ALTER TABLE meme ADD user_id_id INT NOT NULL, ADD template_id_id INT NOT NULL, DROP user_id, DROP template_id');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F79349D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meme ADD CONSTRAINT FK_4B9F79344C924D98 FOREIGN KEY (template_id_id) REFERENCES template (id)');
        $this->addSql('CREATE INDEX IDX_4B9F79349D86650F ON meme (user_id_id)');
        $this->addSql('CREATE INDEX IDX_4B9F79344C924D98 ON meme (template_id_id)');
    }
}
