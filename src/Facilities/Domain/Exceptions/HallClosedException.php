<?php

namespace App\Facilities\Domain\Exceptions;

use App\Facilities\Domain\Values\HallId;

class HallClosedException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
    ) {
        parent::__construct("The hall $hallId is closed");
    }
}
