<?php

namespace App\Facilities\Domain\Entities;

use App\Core\Domain\DateTimeRange;
use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Exceptions\TimeOutOfScopeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\Reservations;

class Calendar
{
    private Reservations $reservations;

    /**
     * @param iterable<Reservation> $reservations
     *
     * @throws InvalidValueException
     */
    public function __construct(
        private HallId $hallId,
        private DateTimeRange $time,
        iterable $reservations = [],
    ) {
        $this->reservations = new Reservations($reservations);
        $this->reservations->each(function (Reservation $reservation) {
            $this->assertHallMatches($reservation->getHallId());
        });
    }

    /**
     * @throws TimeOutOfScopeException
     * @throws UnavailableTimeException
     * @throws InvalidValueException
     */
    public function addReservation(Reservation $reservation): void
    {
        $hallId = $reservation->getHallId();
        $this->assertHallMatches($hallId);

        $time = $reservation->getTime();
        $this->assertTimeInScope($time);
        $this->assertTimeAvailable($time);

        $this->reservations->add($reservation);
    }

    /**
     * @throws InvalidValueException
     */
    private function assertHallMatches(HallId $hallId): void
    {
        if (!$this->hallId->equals($hallId)) {
            throw new InvalidValueException('Reservation hall ID mismatches calendar hall ID.');
        }
    }

    /**
     * @throws TimeOutOfScopeException
     */
    private function assertTimeInScope(DateTimeRange $time): void
    {
        if (!$time->fitsIn($this->time)) {
            throw new TimeOutOfScopeException($time);
        }
    }

    /**
     * @throws UnavailableTimeException
     */
    private function assertTimeAvailable(DateTimeRange $time): void
    {
        $confirmedReservations = $this->reservations
            ->filter(fn (Reservation $reservation) => $reservation->isConfirmed());

        foreach ($confirmedReservations as $reservation) {
            if ($reservation->getTime()->overlaps($time)) {
                throw new UnavailableTimeException($this->hallId, $time);
            }
        }
    }
}
