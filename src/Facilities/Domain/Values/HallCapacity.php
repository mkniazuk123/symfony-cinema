<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\IntegerValue;

readonly class HallCapacity extends IntegerValue
{
    protected static function validate(int $value): void
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Hall capacity cannot be less than 1');
        }
    }
}
