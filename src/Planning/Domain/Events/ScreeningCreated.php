<?php

namespace App\Planning\Domain\Events;

use App\Core\Domain\DateTimeRange;
use App\Core\Domain\DomainEvent;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;

readonly class ScreeningCreated implements DomainEvent
{
    public function __construct(
        public ScreeningId $id,
        public HallId $hallId,
        public MovieId $movieId,
        public DateTimeRange $time,
    ) {
    }
}
