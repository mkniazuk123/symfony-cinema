<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\IntegerValue;
use App\Core\Domain\InvalidValueException;

readonly class HallCapacity extends IntegerValue
{
    /**
     * @throws InvalidValueException
     */
    protected static function validate(int $value): void
    {
        if ($value < 1) {
            throw new InvalidValueException('Hall capacity cannot be less than 1');
        }
    }
}
