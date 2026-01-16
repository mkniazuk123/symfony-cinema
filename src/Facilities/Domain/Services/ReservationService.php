<?php

namespace App\Facilities\Domain\Services;

use App\Core\Domain\Clock;
use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Exceptions\HallClosedException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Ports\CalendarRepository;

class ReservationService
{
    public function __construct(
        private CalendarRepository $calendarRepository,
        private Clock $clock,
    ) {
    }

    /**
     * @throws HallClosedException
     * @throws InvalidTimeException
     * @throws UnavailableTimeException
     */
    public function createReservation(Hall $hall, DateTimeRange $time): Reservation
    {
        if (!$hall->isOpen()) {
            throw new HallClosedException($hall->getId());
        }

        if (!$time->isFuture($this->clock)) {
            throw new InvalidTimeException($time, 'Cannot create reservation for past time.');
        }

        $calendar = $this->calendarRepository->getCalendar($hall->getId(), $time);
        $reservation = Reservation::create($hall->getId(), $time);

        try {
            $reservation->confirm($calendar);
        } catch (UnavailableTimeException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to create reservation.', previous: $exception);
        }

        return $reservation;
    }

    /**
     * @throws InvalidReservationStatusException
     * @throws InvalidTimeException
     */
    public function cancelReservation(Reservation $reservation): void
    {
        $reservation->cancel($this->clock);
    }
}
