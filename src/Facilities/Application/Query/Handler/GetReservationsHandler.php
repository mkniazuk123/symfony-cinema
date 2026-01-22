<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Model\ReservationListDto;
use App\Facilities\Application\Ports\ReservationReadModel;
use App\Facilities\Application\Query\GetReservationsQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetReservationsHandler
{
    public function __construct(
        private ReservationReadModel $reservationReadModel,
    ) {
    }

    public function __invoke(GetReservationsQuery $query): ReservationListDto
    {
        return $this->reservationReadModel->readReservations();
    }
}
