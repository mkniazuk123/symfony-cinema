<?php

namespace App\Tests\Facilities\Application\Command;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Application\Command\CancelReservationCommand;
use App\Facilities\Application\Command\CreateReservationCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Domain\Exceptions\HallClosedException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Ports\ReservationRepository;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;
use App\Tests\Facilities\Fixtures\HallBuilder;
use App\Tests\Facilities\Fixtures\ReservationBuilder;
use App\Tests\IntegrationTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class ReservationCommandIntegrationTest extends IntegrationTestCase
{
    private ReservationRepository $reservationRepository;
    private HallRepository $hallRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();
        $this->reservationRepository = $container->get(ReservationRepository::class);
        $this->hallRepository = $container->get(HallRepository::class);
    }

    public function testCreateReservation(): void
    {
        // Arrange:
        $id = ReservationId::generate();
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $time = DateTimeRange::parse('2100-07-01T10:00:00+00:00', '2100-07-01T12:00:00+00:00');

        // Act:
        $this->commandBus->dispatch(new CreateReservationCommand($id, $hall->getId(), $time));

        // Assert:
        $createdReservation = $this->reservationRepository->find($id);
        $this->assertNotNull($createdReservation);

        $this->assertEquals($hall->getId(), $createdReservation->getHallId());
        $this->assertEquals($time, $createdReservation->getTime());
        $this->assertEquals(ReservationStatus::CONFIRMED, $createdReservation->getStatus());
    }

    public function testCannotCreateReservationForNonExistentHall(): void
    {
        // Arrange:
        $hallId = HallId::generate();
        $time = DateTimeRange::parse('2100-07-01T10:00:00+00:00', '2100-07-01T12:00:00+00:00');

        // Assert:
        $this->expectException(HallNotFoundException::class);

        // Act:
        $this->commandBus->dispatch(new CreateReservationCommand(ReservationId::generate(), $hallId, $time));
    }

    public function testCannotCreateReservationForClosedHall(): void
    {
        // Arrange:
        $hall = new HallBuilder()->closed()->build();
        $this->hallRepository->save($hall);

        $time = DateTimeRange::parse('2100-07-01T10:00:00+00:00', '2100-07-01T12:00:00+00:00');

        // Assert:
        $this->expectException(HallClosedException::class);

        // Act:
        $this->commandBus->dispatch(new CreateReservationCommand(ReservationId::generate(), $hall->getId(), $time));
    }

    public function testCannotCreateReservationForPastTime(): void
    {
        // Arrange:
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $time = DateTimeRange::parse('2000-01-01T10:00:00+00:00', '2000-01-01T12:00:00+00:00');

        // Assert:
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('Cannot create reservation for past time.');

        // Act:
        $this->commandBus->dispatch(new CreateReservationCommand(ReservationId::generate(), $hall->getId(), $time));
    }

    public function testCannotCreateReservationForUnavailableTime(): void
    {
        // Arrange:
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $existingReservation = new ReservationBuilder()
            ->confirmed()
            ->withHallId($hall->getId())
            ->withTime(DateTimeRange::parse('2100-07-01T11:00:00+00:00', '2100-07-01T13:00:00+00:00'))
            ->build();
        $this->reservationRepository->save($existingReservation);

        $newReservationTime = DateTimeRange::parse('2100-07-01T10:00:00+00:00', '2100-07-01T12:00:00+00:00');

        // Assert:
        $this->expectException(UnavailableTimeException::class);

        // Act:
        $this->commandBus->dispatch(new CreateReservationCommand(ReservationId::generate(), $hall->getId(), $newReservationTime));
    }

    public function testCancelReservation(): void
    {
        // Arrange:
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $reservation = new ReservationBuilder()
            ->withHallId($hall->getId())
            ->confirmed()
            ->build();
        $this->reservationRepository->save($reservation);

        // Act:
        $this->commandBus->dispatch(new CancelReservationCommand($reservation->getId()));

        // Assert:
        $cancelledReservation = $this->reservationRepository->find($reservation->getId());
        $this->assertNotNull($cancelledReservation);
        $this->assertEquals(ReservationStatus::CANCELLED, $cancelledReservation->getStatus());
    }

    public function testCannotCancelNonexistentReservation(): void
    {
        // Arrange:
        $reservationId = ReservationId::generate();

        // Assert:
        $this->expectException(ReservationNotFoundException::class);

        // Act:
        $this->commandBus->dispatch(new CancelReservationCommand($reservationId));
    }

    #[TestWith([ReservationStatus::DRAFT])]
    #[TestWith([ReservationStatus::CANCELLED])]
    public function testCannotCancelReservation(ReservationStatus $status): void
    {
        // Arrange:
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $reservation = new ReservationBuilder()
            ->withHallId($hall->getId())
            ->withStatus($status)
            ->build();
        $this->reservationRepository->save($reservation);

        // Assert:
        $this->expectException(InvalidReservationStatusException::class);

        // Act:
        $this->commandBus->dispatch(new CancelReservationCommand($reservation->getId()));
    }

    public function testCannotCancelPastReservation(): void
    {
        // Arrange:
        $hall = new HallBuilder()->open()->build();
        $this->hallRepository->save($hall);

        $reservation = new ReservationBuilder()
            ->withHallId($hall->getId())
            ->withTime(DateTimeRange::parse('2000-01-01T10:00:00+00:00', '2000-01-01T12:00:00+00:00'))
            ->confirmed()
            ->build();
        $this->reservationRepository->save($reservation);

        // Assert:
        $this->expectException(InvalidTimeException::class);
        $this->expectExceptionMessage('Cannot cancel reservation for past time.');

        // Act:
        $this->commandBus->dispatch(new CancelReservationCommand($reservation->getId()));
    }
}
