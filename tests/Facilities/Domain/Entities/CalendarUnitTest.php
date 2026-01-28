<?php

namespace App\Tests\Facilities\Domain\Entities;

use App\Core\Domain\DateTimeRange;
use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Entities\Calendar;
use App\Facilities\Domain\Exceptions\TimeConflictException;
use App\Facilities\Domain\Exceptions\TimeOutOfScopeException;
use App\Facilities\Domain\Values\HallId;
use App\Tests\Facilities\Fixtures\ReservationBuilder;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class CalendarUnitTest extends TestCase
{
    public function testCannotCreateWithReservationForAnotherHall(): void
    {
        // Arrange:
        $calendarHallId = HallId::generate();
        $otherHallId = HallId::generate();
        $time = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-02T12:00:00Z');
        $reservation = new ReservationBuilder()
            ->withHallId($otherHallId)
            ->withTime($time)
            ->build();

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        new Calendar($calendarHallId, $time, [$reservation]);
    }

    public function testCannotAddReservationForAnotherHall(): void
    {
        // Arrange"
        $calendarHallId = HallId::generate();
        $otherHallId = HallId::generate();
        $time = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-02T12:00:00Z');
        $calendar = new Calendar($calendarHallId, $time);
        $reservation = new ReservationBuilder()
            ->withHallId($otherHallId)
            ->build();

        // Assert:
        $this->expectException(InvalidValueException::class);

        // Act:
        $calendar->addReservation($reservation);
    }

    #[TestWith(['2024-12-31T12:00:00Z', '2025-01-01T11:59:59Z'], 'before range')]
    #[TestWith(['2025-01-05T12:00:01Z', '2025-01-06T12:00:00Z'], 'after range')]
    #[TestWith(['2024-12-31T12:00:00Z', '2025-01-06T12:00:00Z'], 'encompassing range')]
    #[TestWith(['2025-01-01T11:00:00Z', '2025-01-03T12:00:00Z'], 'too early start')]
    #[TestWith(['2025-01-03T12:00:00Z', '2025-01-05T13:00:00Z'], 'too late end')]
    public function testCannotAddReservationOutOfTheTimeScope(string $start, string $end): void
    {
        // Arrange:
        $hallId = HallId::generate();
        $calendarTime = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-05T12:00:00Z');
        $calendar = new Calendar($hallId, $calendarTime);

        $reservation = new ReservationBuilder()
            ->withHallId($hallId)
            ->withTime(DateTimeRange::parse($start, $end))
            ->confirmed()
            ->build();

        // Assert:
        $this->expectException(TimeOutOfScopeException::class);

        // Act:
        $calendar->addReservation($reservation);
    }

    public function testCannotAddReservationWithOverlappingTime(): void
    {
        // Arrange
        $hallId = HallId::generate();
        $calendarTime = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-05T12:00:00Z');
        $calendar = new Calendar($hallId, $calendarTime);

        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-02T12:00:00Z', '2025-01-03T12:00:00Z'))
                ->confirmed()
                ->build()
        );

        // Assert:
        $this->expectException(TimeConflictException::class);

        // Act:
        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-03T11:00:00Z', '2025-01-04T12:00:00Z'))
                ->confirmed()
                ->build()
        );
    }

    public function testAddReservation(): void
    {
        // Arrange
        $hallId = HallId::generate();
        $calendarTime = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-05T12:00:00Z');
        $calendar = new Calendar($hallId, $calendarTime);

        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-02T12:00:00Z', '2025-01-03T12:00:00Z'))
                ->confirmed()
                ->build()
        );

        // Act:
        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-03T12:00:00Z', '2025-01-04T12:00:00Z'))
                ->confirmed()
                ->build()
        );

        // Assert:
        $this->addToAssertionCount(1); // If no exception is thrown, the test passes.
    }

    public function testDraftAndCancelledReservationsAreIgnored(): void
    {
        // Arrange
        $hallId = HallId::generate();
        $calendarTime = DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-05T12:00:00Z');
        $calendar = new Calendar($hallId, $calendarTime);

        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-02T12:00:00Z', '2025-01-03T12:00:00Z'))
                ->draft()
                ->build()
        );

        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-03T12:00:00Z', '2025-01-04T12:00:00Z'))
                ->cancelled()
                ->build()
        );

        // Act:
        $calendar->addReservation(
            new ReservationBuilder()
                ->withHallId($hallId)
                ->withTime(DateTimeRange::parse('2025-01-01T12:00:00Z', '2025-01-04T12:00:00Z'))
                ->confirmed()
                ->build()
        );

        // Assert:
        $this->addToAssertionCount(1); // If no exception is thrown, the test passes.
    }
}
