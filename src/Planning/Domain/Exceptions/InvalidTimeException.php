<?php

namespace App\Planning\Domain\Exceptions;

use App\Core\Domain\DateTime;

class InvalidTimeException extends \RuntimeException
{
    public function __construct(
        public readonly DateTime $time,
        string $message,
    ) {
        parent::__construct($message);
    }
}
