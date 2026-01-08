<?php

namespace App\Catalog\Domain\Values;

readonly class MovieDetails
{
    public function __construct(
        public MovieTitle $title,
        public MovieDescription $description,
    ) {
    }
}
