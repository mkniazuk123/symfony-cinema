<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatingLayout;
use App\Tests\Facilities\Fixtures\RowLayoutBuilder;
use App\Tests\Facilities\Fixtures\SeatGroupBuilder;
use PHPUnit\Framework\TestCase;

class SeatingLayoutUnitTest extends TestCase
{
    public function testCannotBeEmpty(): void
    {
        $this->expectException(InvalidValueException::class);

        new SeatingLayout([]);
    }

    public function testRowsMustBeAList(): void
    {
        // Arrange:
        $rows = [
            0 => new RowLayoutBuilder(1)
                ->addSegment(new SeatGroupBuilder()->withSeat(1)->build())
                ->build(),
            2 => new RowLayoutBuilder(2)
                ->addSegment(new SeatGroupBuilder()->withSeat(1)->build())
                ->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        // @phpstan-ignore-next-line
        new SeatingLayout($rows);
    }

    public function testCannotHaveDuplicateRowNumbers(): void
    {
        // Arrange:
        $rows = [
            new RowLayoutBuilder(1)
                ->addSegment(new SeatGroupBuilder()->withSeat(1)->build())
                ->build(),
            new RowLayoutBuilder(1)
                ->addSegment(new SeatGroupBuilder()->withSeat(2)->build())
                ->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new SeatingLayout($rows);
    }

    public function testValidSeatingLayout(): void
    {
        // Arrange:
        $rows = [
            new RowLayoutBuilder(1)
                ->addSegment(new SeatGroupBuilder()->withSeats(1, 2)->build())
                ->build(),
            new RowLayoutBuilder(2)
                ->addSegment(new SeatGroupBuilder()->withSeats(1, 2, 3)->build())
                ->build(),
        ];

        // Act:
        $seatingLayout = new SeatingLayout($rows);

        // Assert:
        $this->assertEquals($rows, $seatingLayout->rows);
        $this->assertEquals(5, $seatingLayout->countSeats());
    }
}
