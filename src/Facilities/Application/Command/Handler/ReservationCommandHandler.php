<?php

namespace App\Facilities\Application\Command\Handler;

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
use App\Facilities\Domain\Services\ReservationService as ReservationDomainService;
use App\Facilities\Domain\Values\ReservationId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class ReservationCommandHandler
{
    public function __construct(
        private HallRepository $hallRepository,
        private ReservationRepository $reservationRepository,
        private ReservationDomainService $reservationDomainService,
    ) {
    }

    /**
     * @throws HallNotFoundException
     * @throws HallClosedException
     * @throws InvalidTimeException
     * @throws UnavailableTimeException
     */
    #[AsMessageHandler]
    public function createReservation(CreateReservationCommand $command): ReservationId
    {
        $hall = $this->hallRepository->find($command->hallId) ?? throw new HallNotFoundException($command->hallId);
        $reservation = $this->reservationDomainService->createReservation($command->id, $hall, $command->time);
        $this->reservationRepository->save($reservation);

        return $reservation->getId();
    }

    /**
     * @throws ReservationNotFoundException
     * @throws InvalidReservationStatusException
     * @throws InvalidTimeException
     */
    #[AsMessageHandler]
    public function cancelReservation(CancelReservationCommand $command): void
    {
        $reservation = $this->reservationRepository->find($command->reservationId) ?? throw new ReservationNotFoundException($command->reservationId);
        $this->reservationDomainService->cancelReservation($reservation);
        $this->reservationRepository->save($reservation);
    }
}
