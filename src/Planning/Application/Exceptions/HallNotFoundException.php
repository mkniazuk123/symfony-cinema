<?php

namespace App\Planning\Application\Exceptions;

use App\Planning\Domain\Values\HallId;

class HallNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
    ) {
        parent::__construct("Hall $hallId not found.");
    }
}
