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

        $this->addSql(<<<SQL
CREATE TABLE facilities_reservation (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    hall_id CHARACTER VARYING NOT NULL,
    time_start TIMESTAMP(0) WITH TIME ZONE NOT NULL,
    time_end TIMESTAMP(0) WITH TIME ZONE NOT NULL,
    status CHARACTER VARYING NOT NULL
);
SQL);

        $this->addSql(<<<SQL
CREATE TABLE planning_hall (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    open BOOLEAN NOT NULL
);
SQL);

        $this->addSql(<<<SQL
CREATE TABLE planning_movie (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    duration INTEGER NOT NULL,
    available BOOLEAN NOT NULL
);
SQL);

        $this->addSql(<<<SQL
CREATE TABLE planning_screening (
    id CHARACTER VARYING NOT NULL PRIMARY KEY,
    hall_id CHARACTER VARYING NOT NULL REFERENCES planning_hall (id),
    movie_id CHARACTER VARYING NOT NULL REFERENCES planning_movie (id),
    time_start TIMESTAMP(0) WITH TIME ZONE NOT NULL,
    time_end TIMESTAMP(0) WITH TIME ZONE NOT NULL
);
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE catalog_movie');
        $this->addSql('DROP TABLE facilities_hall');
        $this->addSql('DROP TABLE facilities_reservation');
        $this->addSql('DROP TABLE planning_screening');
        $this->addSql('DROP TABLE planning_movie');
        $this->addSql('DROP TABLE planning_hall');
    }
}
