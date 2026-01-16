<?php

namespace App\Facilities\Domain\Entities;

use App\Core\Domain\Clock;
use App\Core\Domain\DateTimeRange;
use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\TimeOutOfScopeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;

class Reservation
{
    public static function create(
        HallId $hallId,
        DateTimeRange $time,
    ): self {
        $id = ReservationId::generate();
        $status = ReservationStatus::DRAFT;

        return new self($id, $hallId, $time, $status);
    }

    public static function reconstitute(
        ReservationId $id,
        HallId $hallId,
        DateTimeRange $time,
        ReservationStatus $status,
    ): self {
        return new self($id, $hallId, $time, $status);
    }

    private function __construct(
        private ReservationId $id,
        private HallId $hallId,
        private DateTimeRange $time,
        private ReservationStatus $status,
    ) {
    }

    public function getId(): ReservationId
    {
        return $this->id;
    }

    public function getHallId(): HallId
    {
        return $this->hallId;
    }

    public function getTime(): DateTimeRange
    {
        return $this->time;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function isDraft(): bool
    {
        return ReservationStatus::DRAFT === $this->status;
    }

    public function isConfirmed(): bool
    {
        return ReservationStatus::CONFIRMED === $this->status;
    }

    public function isFuture(Clock $clock): bool
    {
        return $this->time->isFuture($clock);
    }

    /**
     * @throws InvalidReservationStatusException
     * @throws TimeOutOfScopeException
     * @throws UnavailableTimeException
     * @throws InvalidValueException
     */
    public function confirm(Calendar $calendar): void
    {
        if (!$this->isDraft()) {
            throw new InvalidReservationStatusException($this->id, $this->status, 'Only draft reservations can be confirmed.');
        }

        $calendar->addReservation($this);
        $this->status = ReservationStatus::CONFIRMED;
    }

    /**
     * @throws InvalidReservationStatusException
     * @throws InvalidTimeException
     */
    public function cancel(Clock $clock): void
    {
        if (!$this->isConfirmed()) {
            throw new InvalidReservationStatusException($this->id, $this->status, 'Only confirmed reservations can be cancelled.');
        } elseif (!$this->isFuture($clock)) {
            throw new InvalidTimeException($this->time, 'Cannot cancel reservation for past time.');
        }

        $this->status = ReservationStatus::CANCELLED;
    }
}
