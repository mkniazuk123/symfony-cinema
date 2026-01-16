<?php

namespace App\Facilities\Infrastructure;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Ports\ReservationRepository;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;
use Doctrine\DBAL\Connection;

class DbalReservationRepository implements ReservationRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function find(ReservationId $id): ?Reservation
    {
        if (!$this->connection->isTransactionActive()) {
            throw new \RuntimeException(sprintf('%s::%s() must be called inside a transaction.', __CLASS__, __METHOD__));
        }

        $row = $this->fetchRow($id);
        if (null !== $row) {
            return $this->reconstituteFromRow($row);
        } else {
            return null;
        }
    }

    public function save(Reservation $reservation): void
    {
        $this->upsert($reservation);
    }

    private function fetchRow(ReservationId $id): ?array
    {
        $result = $this->connection->executeQuery(<<<SQL
SELECT id, hall_id, time_start, time_end, status
FROM facilities_reservation
WHERE id = ?
FOR UPDATE
SQL,
            [$id],
        );

        return $result->fetchAssociative() ?: null;
    }

    private function reconstituteFromRow(array $row): Reservation
    {
        return Reservation::reconstitute(
            id: new ReservationId($row['id']),
            hallId: new HallId($row['hall_id']),
            time: DateTimeRange::parse($row['time_start'], $row['time_end']),
            status: ReservationStatus::from($row['status']),
        );
    }

    private function upsert(Reservation $reservation): void
    {
        $this->connection->executeStatement(<<<SQL
INSERT INTO facilities_reservation (id, hall_id, time_start, time_end, status)
VALUES (?, ?, ?, ?, ?)
ON CONFLICT (id)
DO UPDATE
SET hall_id = EXCLUDED.hall_id,
    time_start = EXCLUDED.time_start,
    time_end = EXCLUDED.time_end,
    status = EXCLUDED.status
SQL,
            [
                $reservation->getId(),
                $reservation->getHallId(),
                $reservation->getTime()->start,
                $reservation->getTime()->end,
                $reservation->getStatus()->value,
            ],
        );
    }
}
