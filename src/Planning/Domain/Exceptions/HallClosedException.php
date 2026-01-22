<?php

namespace App\Planning\Domain\Exceptions;

use App\Planning\Domain\Values\HallId;

class HallClosedException extends \RuntimeException
{
    public function __construct(
        public readonly HallId $hallId,
    ) {
        parent::__construct("The hall $hallId is closed");
    }
}
