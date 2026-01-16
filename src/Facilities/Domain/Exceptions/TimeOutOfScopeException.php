<?php

namespace App\Facilities\Domain\Exceptions;

use App\Core\Domain\DateTimeRange;

class TimeOutOfScopeException extends \RuntimeException
{
    public function __construct(
        public readonly DateTimeRange $time,
    ) {
        parent::__construct();
    }
}
