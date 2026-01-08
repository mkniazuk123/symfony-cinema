<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatGroup;
use App\Facilities\Domain\Values\SeatNumber;

class SeatGroupBuilder
{
    /** @var list<SeatNumber> */
    private array $seats;

    public function __construct()
    {
        $this->seats = [];
    }

    public function withSeat(SeatNumber|int $seat): self
    {
        if (is_int($seat)) {
            $seat = new SeatNumber($seat);
        }
        $this->seats[] = $seat;

        return $this;
    }

    public function withSeats(SeatNumber|int ...$seats): self
    {
        foreach ($seats as $seat) {
            $this->withSeat($seat);
        }

        return $this;
    }

    /**
     * @throws InvalidValueException
     */
    public function build(): SeatGroup
    {
        return new SeatGroup($this->seats);
    }
}
