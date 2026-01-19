<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;

class NewReservationBuilder
{
    private ReservationId $id;
    private HallId $hallId;
    private DateTimeRange $time;

    public function __construct()
    {
        $this->id = ReservationId::generate();
        $this->hallId = HallId::generate();
        $this->time = new DateTimeRange(
            new DateTime(new \DateTimeImmutable('+1 day')),
            new DateTime(new \DateTimeImmutable('+2 days'))
        );
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

    public function build(): Reservation
    {
        return Reservation::create($this->id, $this->hallId, $this->time);
    }
}
