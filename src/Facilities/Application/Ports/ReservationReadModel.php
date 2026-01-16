<?php

namespace App\Facilities\Application\Ports;

use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Application\Model\ReservationListDto;
use App\Facilities\Domain\Values\ReservationId;

interface ReservationReadModel
{
    public function readReservation(ReservationId $id): ?ReservationDto;

    public function readReservations(): ReservationListDto;
}
