<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Application\Model\ReservationListDto;
use App\Facilities\Application\Ports\ReservationReadModel;
use App\Facilities\Application\Query\GetReservationQuery;
use App\Facilities\Application\Query\GetReservationsQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class ReservationQueryHandler
{
    public function __construct(
        private ReservationReadModel $reservationReadModel,
    ) {
    }

    /**
     * @throws ReservationNotFoundException
     */
    #[AsMessageHandler]
    public function getReservation(GetReservationQuery $query): ReservationDto
    {
        return $this->reservationReadModel->readReservation($query->reservationId)
            ?? throw new ReservationNotFoundException($query->reservationId);
    }

    #[AsMessageHandler]
    public function getReservations(GetReservationsQuery $query): ReservationListDto
    {
        return $this->reservationReadModel->readReservations();
    }
}
