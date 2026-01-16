<?php

namespace App\Facilities\Infrastructure;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Application\Model\ReservationListDto;
use App\Facilities\Application\Ports\ReservationReadModel;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;
use Doctrine\DBAL\Connection;

class DbalReservationReadModel implements ReservationReadModel
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function readReservation(ReservationId $id): ?ReservationDto
    {
        $row = $this->fetchReservationRow($id);
        if (null !== $row) {
            return $this->createReservationDtoFromRow($row);
        } else {
            return null;
        }
    }

    public function readReservations(): ReservationListDto
    {
        $total = $this->countReservations();
        $items = array_map(
            fn (array $row) => $this->createReservationDtoFromRow($row),
            $this->fetchReservationsRows(),
        );

        return new ReservationListDto($total, $items);
    }

    private function fetchReservationRow(ReservationId $id): ?array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, hall_id, time_start, time_end, status
FROM facilities_reservation
WHERE id = ?
SQL,
            [$id],
        );

        return $statement->fetchAssociative() ?: null;
    }

    private function countReservations(): int
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT COUNT(*)
FROM facilities_reservation
SQL
        );

        return (int) $statement->fetchOne();
    }

    private function fetchReservationsRows(): array
    {
        $statement = $this->connection->executeQuery(<<<SQL
SELECT id, hall_id, time_start, time_end, status
FROM facilities_reservation
ORDER BY time_start
SQL
        );

        return $statement->fetchAllAssociative();
    }

    private function createReservationDtoFromRow(array $row): ReservationDto
    {
        return new ReservationDto(
            id: new ReservationId($row['id']),
            hallId: new HallId($row['hall_id']),
            time: DateTimeRange::parse($row['time_start'], $row['time_end']),
            status: ReservationStatus::from($row['status']),
        );
    }
}
