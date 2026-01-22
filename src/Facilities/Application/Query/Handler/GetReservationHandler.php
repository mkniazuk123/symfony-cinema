<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Application\Ports\ReservationReadModel;
use App\Facilities\Application\Query\GetReservationQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetReservationHandler
{
    public function __construct(
        private ReservationReadModel $reservationReadModel,
    ) {
    }

    /**
     * @throws ReservationNotFoundException
     */
    public function __invoke(GetReservationQuery $query): ReservationDto
    {
        return $this->reservationReadModel->readReservation($query->reservationId)
            ?? throw new ReservationNotFoundException($query->reservationId);
    }
}
