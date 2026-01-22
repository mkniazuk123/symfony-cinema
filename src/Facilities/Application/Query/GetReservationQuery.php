<?php

namespace App\Facilities\Application\Query;

use App\Core\Application\Query;
use App\Facilities\Application\Model\ReservationDto;
use App\Facilities\Domain\Values\ReservationId;

/**
 * @implements Query<ReservationDto>
 */
readonly class GetReservationQuery implements Query
{
    public function __construct(
        public ReservationId $reservationId,
    ) {
    }
}
