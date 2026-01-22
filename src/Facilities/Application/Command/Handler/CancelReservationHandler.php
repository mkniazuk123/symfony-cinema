<?php

namespace App\Facilities\Application\Command\Handler;

use App\Facilities\Application\Command\CancelReservationCommand;
use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Ports\ReservationRepository;
use App\Facilities\Domain\Services\ReservationService as ReservationDomainService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CancelReservationHandler
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private ReservationDomainService $reservationDomainService,
    ) {
    }

    /**
     * @throws ReservationNotFoundException
     * @throws InvalidReservationStatusException
     * @throws InvalidTimeException
     */
    public function __invoke(CancelReservationCommand $command): void
    {
        $reservation = $this->reservationRepository->find($command->reservationId) ?? throw new ReservationNotFoundException($command->reservationId);
        $this->reservationDomainService->cancelReservation($reservation);
        $this->reservationRepository->save($reservation);
    }
}
