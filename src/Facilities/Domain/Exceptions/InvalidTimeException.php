<?php

namespace App\Facilities\Domain\Exceptions;

use App\Core\Domain\DateTimeRange;

class InvalidTimeException extends \RuntimeException
{
    public function __construct(
        public readonly DateTimeRange $time,
        string $message,
    ) {
        parent::__construct($message);
    }
}
