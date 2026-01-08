<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\IntegerValue;

readonly class RowNumber extends IntegerValue
{
    protected static function validate(int $value): void
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Row number must be at least 1.');
        }
    }
}
