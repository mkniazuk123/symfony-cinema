<?php

namespace App\Tests\Core\Domain;

use App\Core\Domain\DateTimeRange;
use App\Core\Domain\InvalidValueException;
use App\Tests\Core\Infrastructure\StaticClock;
use PHPUnit\Framework\TestCase;

class DateTimeRangeUnitTest extends TestCase
{
    public function testCannotEndBeforeStarts(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Date time range must not have end before start.');

        DateTimeRange::parse(
            '2024-01-01T12:00:00Z',
            '2024-01-01T11:59:59Z',
        );
    }

    public function testIsFutureIfStartsInTheFuture(): void
    {
        // Act:
        $object = DateTimeRange::parse(
            '2024-01-01T12:00:01Z',
            '2024-01-01T13:00:00Z',
        );

        // Assert:
        $this->assertTrue($object->isFuture(new StaticClock('2024-01-01T12:00:00Z')));
        $this->assertFalse($object->isFuture(new StaticClock('2024-01-01T12:00:01Z')));
        $this->assertFalse($object->isFuture(new StaticClock('2024-01-01T12:00:02Z')));
    }

    public function testOverlaps(): void
    {
        // Act:
        $object = DateTimeRange::parse(
            '2024-01-01T12:00:00Z',
            '2024-01-01T13:00:00Z',
        );

        // Assert:
        $this->assertTrue($object->overlaps(DateTimeRange::parse('2024-01-01T11:59:59Z', '2024-01-01T12:00:01Z')));
        $this->assertTrue($object->overlaps(DateTimeRange::parse('2024-01-01T12:30:00Z', '2024-01-01T12:45:00Z')));
        $this->assertTrue($object->overlaps(DateTimeRange::parse('2024-01-01T12:59:59Z', '2024-01-01T13:00:00Z')));
        $this->assertFalse($object->overlaps(DateTimeRange::parse('2024-01-01T11:00:00Z', '2024-01-01T12:00:00Z')));
        $this->assertFalse($object->overlaps(DateTimeRange::parse('2024-01-01T13:00:00Z', '2024-01-01T14:00:00Z')));
    }

    public function testFitsIn(): void
    {
        // Act:
        $object = DateTimeRange::parse(
            '2024-01-01T12:00:00Z',
            '2024-01-01T13:00:00Z',
        );

        // Assert:
        $this->assertTrue($object->fitsIn(DateTimeRange::parse('2024-01-01T12:00:00Z', '2024-01-01T13:00:00Z')));
        $this->assertTrue($object->fitsIn(DateTimeRange::parse('2024-01-01T11:00:00Z', '2024-01-01T14:00:00Z')));
        $this->assertFalse($object->fitsIn(DateTimeRange::parse('2024-01-01T12:30:00Z', '2024-01-01T13:30:00Z')));
        $this->assertFalse($object->fitsIn(DateTimeRange::parse('2024-01-01T11:30:00Z', '2024-01-01T12:30:00Z')));
        $this->assertFalse($object->fitsIn(DateTimeRange::parse('2024-01-01T12:30:00Z', '2024-01-01T12:45:00Z')));
    }
}
