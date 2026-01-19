<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;

readonly class CreateReservationCommand implements Command
{
    public function __construct(
        public ReservationId $id,
        public HallId $hallId,
        public DateTimeRange $time,
    ) {
    }
}
