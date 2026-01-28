<?php

namespace App\Catalog\API\Model;

readonly class MovieDetails
{
    public function __construct(
        public string $title,
        public string $description,
    ) {
    }
}
