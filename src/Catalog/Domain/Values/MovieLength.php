<?php

namespace App\Catalog\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Core\Domain\Value;

readonly class MovieLength extends Value
{
    public function __construct(
        public int $minutes,
    ) {
        if ($minutes <= 0) {
            throw new InvalidValueException('Movie length must be greater than zero.');
        }
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->minutes === $this->minutes;
    }
}
