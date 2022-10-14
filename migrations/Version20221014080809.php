<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221014080809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add created_at and updated_at columns to question table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE question SET created_at = NOW(), updated_at = NOW()');    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE question DROP created_at, DROP updated_at');
    }
}
