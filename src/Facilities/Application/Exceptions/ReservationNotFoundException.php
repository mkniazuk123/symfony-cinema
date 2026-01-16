<?php

namespace App\Facilities\Application\Exceptions;

use App\Facilities\Domain\Values\ReservationId;

class ReservationNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly ReservationId $reservationId,
    ) {
        parent::__construct("Reservation $reservationId not found.");
    }
}
