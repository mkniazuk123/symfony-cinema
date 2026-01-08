<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version0000000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<SQL
CREATE TABLE catalog_movie (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    status CHARACTER VARYING NOT NULL,
    title CHARACTER VARYING NOT NULL,
    description CHARACTER VARYING NOT NULL,
    length INTEGER NOT NULL
);
SQL);

        $this->addSql(<<<SQL
CREATE TABLE facilities_hall (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    status CHARACTER VARYING NOT NULL,
    name CHARACTER VARYING NOT NULL,
    capacity INTEGER NOT NULL,
    layout JSONB NOT NULL
);
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE catalog_movie');
        $this->addSql('DROP TABLE facilities_hall');
    }
}
