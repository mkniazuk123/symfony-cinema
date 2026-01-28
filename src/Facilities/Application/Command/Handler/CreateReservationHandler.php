<?php

namespace App\Facilities\Application\Command\Handler;

use App\Facilities\Application\Command\CreateReservationCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Domain\Exceptions\HallClosedException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\TimeConflictException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Ports\ReservationRepository;
use App\Facilities\Domain\Services\ReservationService as ReservationDomainService;
use App\Facilities\Domain\Values\ReservationId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateReservationHandler
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
     * @throws TimeConflictException
     */
    public function __invoke(CreateReservationCommand $command): ReservationId
    {
        $hall = $this->hallRepository->find($command->hallId) ?? throw new HallNotFoundException($command->hallId);
        $reservation = $this->reservationDomainService->createReservation($command->id, $hall, $command->time);
        $this->reservationRepository->save($reservation);

        return $reservation->getId();
    }
}
