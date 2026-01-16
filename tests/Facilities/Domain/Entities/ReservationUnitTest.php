<?php

namespace App\Tests\Facilities\Domain\Entities;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Calendar;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Values\ReservationStatus;
use App\Tests\Core\Infrastructure\StaticClock;
use App\Tests\Facilities\Fixtures\NewReservationBuilder;
use App\Tests\Facilities\Fixtures\ReservationBuilder;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ReservationUnitTest extends TestCase
{
    use ProphecyTrait;

    public function testNewReservationIsADraft(): void
    {
        // Act:
        $reservation = new NewReservationBuilder()->build();

        // Assert:
        $this->assertTrue($reservation->isDraft());
    }

    public function testConfirmAddsReservationToCalendar(): void
    {
        // Arrange:
        $calendarMock = $this->prophesize(Calendar::class);
        $reservation = new NewReservationBuilder()->build();

        // Act:
        $reservation->confirm($calendarMock->reveal());

        // Assert:
        $calendarMock->addReservation($reservation)->shouldHaveBeenCalledOnce();
        $this->assertTrue($reservation->isConfirmed());
    }

    #[TestWith([ReservationStatus::CONFIRMED])]
    #[TestWith([ReservationStatus::CANCELLED])]
    public function testOnlyDraftReservationCanBeConfirmed(ReservationStatus $status): void
    {
        // Arrange:
        $calendarMock = $this->prophesize(Calendar::class);
        $reservation = new ReservationBuilder()->withStatus($status)->build();

        // Assert:
        $this->expectException(InvalidReservationStatusException::class);

        // Act:
        $reservation->confirm($calendarMock->reveal());
    }

    #[TestWith([ReservationStatus::DRAFT])]
    #[TestWith([ReservationStatus::CANCELLED])]
    public function testOnlyConfirmedReservationCanBeCancelled(ReservationStatus $status): void
    {
        // Arrange:
        $clock = new StaticClock();
        $reservation = new ReservationBuilder()->withStatus($status)->build();

        // Assert:
        $this->expectException(InvalidReservationStatusException::class);

        // Act:
        $reservation->cancel($clock);
    }

    public function testCannotConfirmPastTimeReservation(): void
    {
        // Arrange:
        $reservation = new ReservationBuilder()
            ->confirmed()
            ->withTime(DateTimeRange::parse('2025-01-01T10:00:00+00:00', '2025-01-02T10:00:00+00:00'))
            ->build();

        $clock = new StaticClock('2025-01-01T10:00:00+00:00');

        // Assert:
        $this->expectException(InvalidTimeException::class);

        // Act:
        $reservation->cancel($clock);
    }

    public function testCancel(): void
    {
        // Arrange:
        $reservation = new ReservationBuilder()
            ->confirmed()
            ->withTime(DateTimeRange::parse('2025-01-02T10:00:00+00:00', '2025-01-03T10:00:00+00:00'))
            ->build();

        $clock = new StaticClock('2025-01-02T09:59:59+00:00');

        // Act:
        $reservation->cancel($clock);

        // Assert:
        $this->assertEquals(ReservationStatus::CANCELLED, $reservation->getStatus());
    }
}
