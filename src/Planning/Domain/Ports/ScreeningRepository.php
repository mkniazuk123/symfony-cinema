<?php

namespace App\Planning\Domain\Ports;

use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Entities\Screening;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\ScreeningId;

interface ScreeningRepository
{
    public function find(ScreeningId $id): ?Screening;

    public function save(Screening $screening): void;

    public function hasConflict(HallId $hallId, DateTimeRange $time): bool;
}
