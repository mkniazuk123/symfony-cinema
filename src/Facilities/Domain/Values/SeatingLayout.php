<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;

readonly class SeatingLayout
{
    /**
     * @param list<RowLayout> $rows
     *
     * @throws InvalidValueException
     */
    public function __construct(public array $rows)
    {
        $this->assertList();
        $this->assertNotEmpty();
        $this->assertUniqueRows();
    }

    public function countSeats(): int
    {
        return array_sum(
            array_map(
                fn (RowLayout $row) => $row->countSeats(),
                $this->rows,
            ),
        );
    }

    /**
     * @throws InvalidValueException
     */
    private function assertList(): void
    {
        if (!array_is_list($this->rows)) {
            throw new InvalidValueException('Seating layout rows must be a list');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertNotEmpty(): void
    {
        if (empty($this->rows)) {
            throw new InvalidValueException('Seating layout cannot be empty');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertUniqueRows(): void
    {
        $rowNumbers = [];
        foreach ($this->rows as $row) {
            $number = $row->number->value();
            if (in_array($number, $rowNumbers, true)) {
                throw new InvalidValueException("Duplicate row number $number");
            }
            $rowNumbers[] = $number;
        }
    }
}
