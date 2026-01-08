<?php

namespace App\Facilities\Domain\Exceptions;

use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallStatus;

class InvalidHallStatusException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
        public readonly HallStatus $status,
    ) {
        parent::__construct("Invalid status $status->value for hall $hallId");
    }
}
