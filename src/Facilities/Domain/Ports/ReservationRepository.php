<?php

namespace App\Facilities\Domain\Ports;

use App\Facilities\Domain\Entities\Reservation;
use App\Facilities\Domain\Values\ReservationId;

interface ReservationRepository
{
    public function find(ReservationId $id): ?Reservation;

    public function save(Reservation $reservation): void;
}
