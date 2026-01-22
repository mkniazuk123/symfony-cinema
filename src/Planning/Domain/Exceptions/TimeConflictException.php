<?php

namespace App\Planning\Domain\Exceptions;

use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Values\HallId;

class TimeConflictException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
        public readonly DateTimeRange $time,
    ) {
        parent::__construct();
    }
}
