<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;

readonly class SeatGroup implements RowSegment
{
    /**
     * @param list<SeatNumber> $seats
     *
     * @throws InvalidValueException
     */
    public function __construct(public array $seats)
    {
        $this->assertList();
        $this->assertNotEmpty();
        $this->assertUniqueNumbers();
    }

    public function countSeats(): int
    {
        return count($this->seats);
    }

    /**
     * @throws InvalidValueException
     */
    private function assertNotEmpty(): void
    {
        if (empty($this->seats)) {
            throw new InvalidValueException('Seat group cannot be empty');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertList(): void
    {
        if (!array_is_list($this->seats)) {
            throw new InvalidValueException('Seat group seats must be a list');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertUniqueNumbers(): void
    {
        $numbers = [];
        foreach ($this->seats as $seat) {
            $number = $seat->value();
            if (in_array($number, $numbers, true)) {
                throw new InvalidValueException("Duplicate seat number $number in group");
            }
            $numbers[] = $number;
        }
    }
}
