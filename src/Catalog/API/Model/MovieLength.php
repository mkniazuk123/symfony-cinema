<?php

namespace App\Catalog\API\Model;

readonly class MovieLength
{
    public function __construct(
        public int $minutes,
    ) {
    }
}
