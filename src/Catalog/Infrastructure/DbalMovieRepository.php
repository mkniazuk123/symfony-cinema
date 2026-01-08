<?php

namespace App\Catalog\Infrastructure;

use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Catalog\Domain\Values\MovieDescription;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Catalog\Domain\Values\MovieTitle;
use Doctrine\DBAL\Connection;

class DbalMovieRepository implements MovieRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function find(MovieId $id): ?Movie
    {
        if (!$this->connection->isTransactionActive()) {
            throw new \RuntimeException(sprintf('%s::%s() must be called inside a transaction.', __CLASS__, __METHOD__));
        }

        $row = $this->fetchRow($id);
        if (null !== $row) {
            return $this->reconstituteMovieFromRow($row);
        } else {
            return null;
        }
    }

    public function save(Movie $movie): void
    {
        $this->connection->executeStatement(<<<SQL
INSERT INTO catalog_movie (id, status, title, description, length)
VALUES (?, ?, ?, ?, ?)
ON CONFLICT (id)
DO UPDATE
SET status = EXCLUDED.status,
    title = EXCLUDED.title,
    description = EXCLUDED.description,
    length = EXCLUDED.length
SQL,
            [
                $movie->getId(),
                $movie->getStatus()->value,
                $movie->getDetails()->title,
                $movie->getDetails()->description,
                $movie->getLength()->minutes,
            ],
        );
    }

    private function fetchRow(MovieId $id): ?array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, status, title, description, length
FROM catalog_movie
WHERE id = ?
FOR UPDATE
SQL,
            [$id],
        );

        return $statement->fetchAssociative() ?: null;
    }

    private function reconstituteMovieFromRow(array $row): Movie
    {
        return Movie::reconstitute(
            id: new MovieId($row['id']),
            status: MovieStatus::from($row['status']),
            details: new MovieDetails(
                title: new MovieTitle($row['title']),
                description: new MovieDescription($row['description']),
            ),
            length: new MovieLength((int) $row['length']),
        );
    }
}
