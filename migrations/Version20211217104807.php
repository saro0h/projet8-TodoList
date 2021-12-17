<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217104807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE todolist_task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE todolist_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE todolist_task (id INT NOT NULL, user_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, is_done BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1EA709CCA76ED395 ON todolist_task (user_id)');
        $this->addSql('CREATE TABLE todolist_user (id INT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(60) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C14A04A0F85E0677 ON todolist_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C14A04A0E7927C74 ON todolist_user (email)');
        $this->addSql('ALTER TABLE todolist_task ADD CONSTRAINT FK_1EA709CCA76ED395 FOREIGN KEY (user_id) REFERENCES todolist_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE todolist_task DROP CONSTRAINT FK_1EA709CCA76ED395');
        $this->addSql('DROP SEQUENCE todolist_task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE todolist_user_id_seq CASCADE');
        $this->addSql('DROP TABLE todolist_task');
        $this->addSql('DROP TABLE todolist_user');
    }
}
