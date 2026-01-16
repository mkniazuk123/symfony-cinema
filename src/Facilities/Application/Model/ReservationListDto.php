<?php

namespace App\Facilities\Application\Model;

readonly class ReservationListDto
{
    public function __construct(
        public int $total,
        /** @var ReservationDto[] */
        public array $items,
    ) {
    }
}
