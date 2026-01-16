<?php

namespace App\Facilities\Application\Model;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Domain\Values\ReservationStatus;

readonly class ReservationDto
{
    public function __construct(
        public ReservationId $id,
        public HallId $hallId,
        public DateTimeRange $time,
        public ReservationStatus $status,
    ) {
    }
}
