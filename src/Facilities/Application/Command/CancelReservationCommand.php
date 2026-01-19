<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Facilities\Domain\Values\ReservationId;

readonly class CancelReservationCommand implements Command
{
    public function __construct(
        public ReservationId $reservationId,
    ) {
    }
}
