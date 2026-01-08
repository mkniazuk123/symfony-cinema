<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\Gap;
use App\Facilities\Domain\Values\RowLayout;
use App\Facilities\Domain\Values\RowNumber;
use App\Tests\Facilities\Fixtures\SeatGroupBuilder;
use PHPUnit\Framework\TestCase;

class RowLayoutUnitTest extends TestCase
{
    public function testMustContainSeatGroup(): void
    {
        // Arrange:
        $segments = [
            new Gap(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new RowLayout(
            number: new RowNumber(1),
            segments: $segments
        );
    }

    public function testCannotHaveDuplicateSeatNumbers(): void
    {
        // Arrange:
        $segments = [
            new SeatGroupBuilder()->withSeats(1, 2)->build(),
            new SeatGroupBuilder()->withSeats(2, 3)->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new RowLayout(
            number: new RowNumber(1),
            segments: $segments,
        );
    }

    public function testSegmentsMustBeAList(): void
    {
        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new RowLayout(
            number: new RowNumber(1),
            // @phpstan-ignore-next-line
            segments: [
                0 => new SeatGroupBuilder()->withSeats(1, 2)->build(),
                2 => new Gap(),
            ]
        );
    }

    public function testCannotHaveTwoGapsNextToEachOther(): void
    {
        // Arrange:
        $segments = [
            new SeatGroupBuilder()->withSeats(1, 2)->build(),
            new Gap(),
            new Gap(),
            new SeatGroupBuilder()->withSeats(3, 4)->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new RowLayout(
            number: new RowNumber(1),
            segments: $segments,
        );
    }

    public function testCannotHaveTwoGroupsNextToEachOther(): void
    {
        // Arrange:
        $segments = [
            new SeatGroupBuilder()->withSeats(1, 2)->build(),
            new SeatGroupBuilder()->withSeats(3, 4)->build(),
        ];

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new RowLayout(
            number: new RowNumber(1),
            segments: $segments,
        );
    }

    public function testValidRowLayout(): void
    {
        // Arrange:
        $segments = [
            new SeatGroupBuilder()->withSeats(1, 2)->build(),
            new Gap(),
            new SeatGroupBuilder()->withSeats(4, 5)->build(),
        ];

        // Act:
        $rowLayout = new RowLayout(
            number: new RowNumber(1),
            segments: $segments,
        );

        // Assert:
        $this->assertEquals($segments, $rowLayout->segments);
        $this->assertEquals(4, $rowLayout->countSeats());
    }
}
