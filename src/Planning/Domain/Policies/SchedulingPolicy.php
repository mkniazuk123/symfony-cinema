<?php

namespace App\Planning\Domain\Policies;

use App\Core\Domain\Duration;

class SchedulingPolicy
{
    public function getMinimumAdvancePeriod(): Duration
    {
        return Duration::days(1);
    }

    public function getMinimumScreeningGap(): Duration
    {
        return Duration::minutes(15);
    }
}
