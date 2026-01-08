<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\RowLayout;
use App\Facilities\Domain\Values\SeatingLayout;

class SeatingLayoutBuilder
{
    /** @var list<RowLayout> */
    private array $rows;

    public function __construct()
    {
        $this->rows = [];
    }

    public function addRow(RowLayout $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    public function addSampleRow(int $seats = 1): self
    {
        $seats = range(1, $seats);

        return $this->addRow(
            new RowLayoutBuilder(1)
                ->addSegment(
                    new SeatGroupBuilder()
                        ->withSeats(...$seats)
                        ->build()
                )
                ->build()
        );
    }

    /**
     * @throws InvalidValueException
     */
    public function build(): SeatingLayout
    {
        return new SeatingLayout($this->rows);
    }
}
