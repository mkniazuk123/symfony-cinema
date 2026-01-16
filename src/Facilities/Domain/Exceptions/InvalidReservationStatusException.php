<?php

namespace App\Facilities\Domain\Exceptions;

use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;

class InvalidReservationStatusException extends \RuntimeException
{
    public function __construct(
        public readonly ReservationId $reservationId,
        public readonly ReservationStatus $status,
        string $message = '',
    ) {
        parent::__construct($message);
    }
}
