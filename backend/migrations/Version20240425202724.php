<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425202724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banned_user DROP FOREIGN KEY FK_50A566A59D86650F');
        $this->addSql('DROP INDEX UNIQ_50A566A59D86650F ON banned_user');
        $this->addSql('ALTER TABLE banned_user CHANGE user_id_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE banned_user ADD CONSTRAINT FK_50A566A5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50A566A5A76ED395 ON banned_user (user_id)');
        $this->addSql('ALTER TABLE user CHANGE reg_date reg_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banned_user DROP FOREIGN KEY FK_50A566A5A76ED395');
        $this->addSql('DROP INDEX UNIQ_50A566A5A76ED395 ON banned_user');
        $this->addSql('ALTER TABLE banned_user CHANGE user_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE banned_user ADD CONSTRAINT FK_50A566A59D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50A566A59D86650F ON banned_user (user_id_id)');
        $this->addSql('ALTER TABLE user CHANGE reg_date reg_date DATETIME DEFAULT NULL');
    }
}
