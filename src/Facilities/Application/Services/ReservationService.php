<?php

namespace App\Facilities\Application\Services;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Application\Model\ReservationListDto;
use App\Facilities\Application\Ports\ReservationReadModel;
use App\Facilities\Domain\Exceptions\HallClosedException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Ports\ReservationRepository;
use App\Facilities\Domain\Services\ReservationService as ReservationDomainService;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use Doctrine\DBAL\Connection;

class ReservationService
{
    public function __construct(
        private Connection $connection,
        private HallRepository $hallRepository,
        private ReservationRepository $reservationRepository,
        private ReservationDomainService $reservationDomainService,
        private ReservationReadModel $reservationReadModel,
    ) {
    }

    /**
     * @throws ReservationNotFoundException
     */
    public function getReservation(ReservationId $reservationId): ReservationDto
    {
        return $this->reservationReadModel->readReservation($reservationId)
            ?? throw new ReservationNotFoundException($reservationId);
    }

    public function getReservations(): ReservationListDto
    {
        return $this->reservationReadModel->readReservations();
    }

    /**
     * @throws HallNotFoundException
     * @throws HallClosedException
     * @throws InvalidTimeException
     * @throws UnavailableTimeException
     */
    public function createReservation(HallId $hallId, DateTimeRange $time): ReservationId
    {
        return $this->connection->transactional(function () use ($hallId, $time) {
            $hall = $this->hallRepository->find($hallId) ?? throw new HallNotFoundException($hallId);
            $reservation = $this->reservationDomainService->createReservation($hall, $time);
            $this->reservationRepository->save($reservation);

            return $reservation->getId();
        });
    }

    /**
     * @throws ReservationNotFoundException
     * @throws InvalidReservationStatusException
     * @throws InvalidTimeException
     */
    public function cancelReservation(ReservationId $reservationId): void
    {
        $this->connection->transactional(function () use ($reservationId) {
            $reservation = $this->reservationRepository->find($reservationId) ?? throw new ReservationNotFoundException($reservationId);
            $this->reservationDomainService->cancelReservation($reservation);
            $this->reservationRepository->save($reservation);
        });
    }
}
