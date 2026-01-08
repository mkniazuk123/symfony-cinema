<?php

namespace App\Core\Domain;

class InvalidValueException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
