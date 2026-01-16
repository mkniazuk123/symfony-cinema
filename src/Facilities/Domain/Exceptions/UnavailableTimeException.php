<?php

namespace App\Facilities\Domain\Exceptions;

use App\Core\Domain\DateTimeRange;
use App\Facilities\Domain\Values\HallId;

class UnavailableTimeException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
        public readonly DateTimeRange $time,
    ) {
        parent::__construct();
    }
}
