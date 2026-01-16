<?php

namespace App\Facilities\Domain\Ports;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Entities\Calendar;
use App\Facilities\Domain\Values\HallId;

interface CalendarRepository
{
    public function getCalendar(HallId $hallId, DateTimeRange $time): Calendar;
}
