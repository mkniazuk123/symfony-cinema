<?php

namespace App\Facilities\Application\Query;

use App\Core\Application\Query;
use App\Facilities\Domain\Values\ReservationId;

readonly class GetReservationQuery implements Query
{
    public function __construct(public ReservationId $reservationId)
    {
    }
}
