<?php

namespace App\Planning\Infrastructure;

use App\Core\Domain\Duration;
use App\Planning\Domain\Entities\Movie;
use App\Planning\Domain\Ports\MovieRepository;
use App\Planning\Domain\Values\MovieId;
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
INSERT INTO planning_movie (id, duration, available)
VALUES (?, ?, ?)
ON CONFLICT (id)
DO UPDATE
SET duration = EXCLUDED.duration,
    available = EXCLUDED.available
SQL,
            [
                $movie->getId(),
                $movie->getDuration()->inSeconds(),
                (int) $movie->isAvailable(),
            ],
        );
    }

    private function fetchRow(MovieId $id): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT id, duration, available
FROM planning_movie
WHERE id = ?
FOR UPDATE
SQL,
            [$id],
        );

        return $result->fetchAssociative() ?: null;
    }

    private function reconstituteMovieFromRow(array $row): Movie
    {
        return Movie::reconstitute(
            id: new MovieId($row['id']),
            duration: Duration::seconds((int) $row['duration']),
            available: (bool) $row['available'],
        );
    }
}
