<?php

namespace App\Facilities\Application\Exceptions;

use App\Facilities\Domain\Values\HallId;

class HallNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
    ) {
        parent::__construct("Hall $hallId not found.");
    }
}
