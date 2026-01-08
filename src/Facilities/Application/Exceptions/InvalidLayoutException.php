<?php

namespace App\Facilities\Application\Exceptions;

class InvalidLayoutException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
