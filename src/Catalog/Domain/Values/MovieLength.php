<?php

namespace App\Catalog\Domain\Values;

readonly class MovieLength
{
    public function __construct(
        public int $minutes,
    ) {
        if ($minutes <= 0) {
            throw new \InvalidArgumentException('Movie length must be greater than zero.');
        }
    }
}
