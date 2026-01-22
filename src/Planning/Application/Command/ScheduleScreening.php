<?php

namespace App\Planning\Application\Command;

use App\Core\Application\Command;
use App\Core\Domain\DateTime;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;

readonly class ScheduleScreening implements Command
{
    public function __construct(
        public ScreeningId $id,
        public HallId $hallId,
        public MovieId $movieId,
        public DateTime $startTime,
    ) {
    }
}
