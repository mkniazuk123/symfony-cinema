<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;

class ReservationBuilder
{
    private ReservationId $id;
    private HallId $hallId;
    private DateTimeRange $time;
    private ReservationStatus $status;

    public function __construct(?ReservationId $id = null)
    {
        $this->id = $id ?? ReservationId::generate();
        $this->hallId = HallId::generate();
        $this->time = new DateTimeRange(
            new DateTime(new \DateTimeImmutable('+1 day')),
            new DateTime(new \DateTimeImmutable('+2 days'))
        );
        $this->status = ReservationStatus::DRAFT;
    }

    public function withId(ReservationId|string $id): self
    {
        if (is_string($id)) {
            $id = new ReservationId($id);
        }
        $this->id = $id;

        return $this;
    }

    public function withHallId(HallId|string $hallId): self
    {
        if (is_string($hallId)) {
            $hallId = new HallId($hallId);
        }
        $this->hallId = $hallId;

        return $this;
    }

    public function withTime(DateTimeRange $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function withStatus(ReservationStatus|string $status): self
    {
        if (is_string($status)) {
            $status = ReservationStatus::from($status);
        }
        $this->status = $status;

        return $this;
    }

    public function draft(): self
    {
        return $this->withStatus(ReservationStatus::DRAFT);
    }

    public function confirmed(): self
    {
        return $this->withStatus(ReservationStatus::CONFIRMED);
    }

    public function cancelled(): self
    {
        return $this->withStatus(ReservationStatus::CANCELLED);
    }

    public function build(): Reservation
    {
        return Reservation::reconstitute($this->id, $this->hallId, $this->time, $this->status);
    }
}
