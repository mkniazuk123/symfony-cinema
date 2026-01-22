<?php

namespace App\Planning\Infrastructure;

use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Entities\Screening;
use App\Planning\Domain\Ports\ScreeningRepository;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;
use Doctrine\DBAL\Connection;

class DbalScreeningRepository implements ScreeningRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function find(ScreeningId $id): ?Screening
    {
        if (!$this->connection->isTransactionActive()) {
            throw new \RuntimeException(sprintf('%s::%s() must be called inside a transaction.', __CLASS__, __METHOD__));
        }

        $row = $this->fetchRow($id);
        if (null !== $row) {
            return $this->reconstituteScreeningFromRow($row);
        } else {
            return null;
        }
    }

    public function save(Screening $screening): void
    {
        $this->connection->executeStatement(<<<SQL
INSERT INTO planning_screening (id, hall_id, movie_id, time_start, time_end)
VALUES (?, ?, ?, ?, ?)
ON CONFLICT (id)
DO UPDATE
SET hall_id = EXCLUDED.hall_id,
    movie_id = EXCLUDED.movie_id,
    time_start = EXCLUDED.time_start,
    time_end = EXCLUDED.time_end
SQL,
            [
                $screening->getId(),
                $screening->getHallId(),
                $screening->getMovieId(),
                (string) $screening->getTime()->start,
                (string) $screening->getTime()->end,
            ],
        );
    }

    public function hasConflict(HallId $hallId, DateTimeRange $time): bool
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT 1
FROM planning_screening
WHERE hall_id = ?
  AND NOT (time_end <= ? OR time_start >= ?)
LIMIT 1
SQL,
            [$hallId, $time->start, $time->end],
        );

        return (bool) $result->fetchOne();
    }

    private function fetchRow(ScreeningId $id): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT id, hall_id, movie_id, time_start, time_end
FROM planning_screening
WHERE id = ?
FOR UPDATE
SQL,
            [$id],
        );

        return $result->fetchAssociative() ?: null;
    }

    private function reconstituteScreeningFromRow(array $row): Screening
    {
        return Screening::reconstitute(
            id: new ScreeningId($row['id']),
            hallId: new HallId($row['hall_id']),
            movieId: new MovieId($row['movie_id']),
            time: DateTimeRange::parse($row['time_start'], $row['time_end']),
        );
    }
}
