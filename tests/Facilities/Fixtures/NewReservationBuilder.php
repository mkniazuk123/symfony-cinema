<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Values\HallId;

class NewReservationBuilder
{
    private HallId $hallId;
    private DateTimeRange $time;

    public function __construct()
    {
        $this->hallId = HallId::generate();
        $this->time = new DateTimeRange(
            new DateTime(new \DateTimeImmutable('+1 day')),
            new DateTime(new \DateTimeImmutable('+2 days'))
        );
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
        return Reservation::create($this->hallId, $this->time);
    }
}
