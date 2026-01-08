<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatGroup;
use App\Facilities\Domain\Values\SeatNumber;
use PHPUnit\Framework\TestCase;

class SeatGroupUnitTest extends TestCase
{
    public function testCannotBeEmpty(): void
    {
        $this->expectException(InvalidValueException::class);

        new SeatGroup([]);
    }

    public function testCannotHaveDuplicateSeatsNumbers(): void
    {
        $this->expectException(InvalidValueException::class);

        new SeatGroup([
            new SeatNumber(1),
            new SeatNumber(2),
            new SeatNumber(1),
        ]);
    }

    public function testSeatsMustBeAList(): void
    {
        $this->expectException(InvalidValueException::class);

        // @phpstan-ignore-next-line
        new SeatGroup([
            0 => new SeatNumber(1),
            2 => new SeatNumber(2),
        ]);
    }

    public function testConsecutiveNumbers(): void
    {
        // Act:
        $seatGroup = new SeatGroup([
            new SeatNumber(1),
            new SeatNumber(2),
            new SeatNumber(3),
        ]);

        // Assert:
        $this->assertEquals(
            [1, 2, 3],
            array_map(fn (SeatNumber $seat) => $seat->value(), $seatGroup->seats)
        );
    }

    public function testRandomNumbers(): void
    {
        // Act:
        $seatGroup = new SeatGroup([
            new SeatNumber(10),
            new SeatNumber(5),
            new SeatNumber(20),
        ]);

        // Assert:
        $this->assertEquals(
            [10, 5, 20],
            array_map(fn (SeatNumber $seat) => $seat->value(), $seatGroup->seats)
        );
    }

    public function testCountSeats(): void
    {
        // Act:
        $seatGroup = new SeatGroup([
            new SeatNumber(1),
            new SeatNumber(2),
            new SeatNumber(3),
        ]);

        // Assert:
        $this->assertEquals(3, $seatGroup->countSeats());
    }
}
