<?php

namespace App\Core\Domain;

abstract readonly class Value
{
    abstract public function equals(Value $other): bool;
}
