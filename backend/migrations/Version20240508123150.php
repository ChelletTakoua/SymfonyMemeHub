<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508123150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banned_user ADD admin_id INT NOT NULL');
        $this->addSql('ALTER TABLE banned_user ADD CONSTRAINT FK_50A566A5642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_50A566A5642B8210 ON banned_user (admin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banned_user DROP FOREIGN KEY FK_50A566A5642B8210');
        $this->addSql('DROP INDEX IDX_50A566A5642B8210 ON banned_user');
        $this->addSql('ALTER TABLE banned_user DROP admin_id');
    }
}
