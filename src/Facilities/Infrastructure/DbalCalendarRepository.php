<?php

namespace App\Facilities\Infrastructure;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Calendar;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Ports\CalendarRepository;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;
use Doctrine\DBAL\Connection;

class DbalCalendarRepository implements CalendarRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getCalendar(HallId $hallId, DateTimeRange $time): Calendar
    {
        if (!$this->connection->isTransactionActive()) {
            throw new \RuntimeException(sprintf('%s::%s() must be called inside a transaction.', __CLASS__, __METHOD__));
        }

        $reservations = $this->getReservations($hallId, $time);

        return new Calendar($hallId, $time, $reservations);
    }

    private function getReservations(HallId $hallId, DateTimeRange $time): array
    {
        $rows = $this->fetchReservationRows($hallId, $time);

        return array_map(fn (array $row) => $this->reconstituteReservationFromRow($row), $rows);
    }

    private function fetchReservationRows(HallId $hallId, DateTimeRange $time): array
    {
        return $this->connection->executeQuery(<<<SQL
SELECT id, hall_id, time_start, time_end, status
FROM facilities_reservation
WHERE hall_id = ?
  AND time_start < ?
  AND time_end > ?
ORDER BY time_start
FOR UPDATE
SQL,
            [$hallId, $time->end, $time->start],
        )->fetchAllAssociative();
    }

    private function reconstituteReservationFromRow(array $row): Reservation
    {
        return Reservation::reconstitute(
            id: new ReservationId($row['id']),
            hallId: new HallId($row['hall_id']),
            time: DateTimeRange::parse($row['time_start'], $row['time_end']),
            status: ReservationStatus::from($row['status']),
        );
    }
}
