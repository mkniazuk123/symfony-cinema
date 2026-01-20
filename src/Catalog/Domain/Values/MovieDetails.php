<?php

namespace App\Catalog\Domain\Values;

use App\Core\Domain\Value;

readonly class MovieDetails extends Value
{
    public function __construct(
        public MovieTitle $title,
        public MovieDescription $description,
    ) {
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self
            && $other->title->equals($this->title)
            && $other->description->equals($this->description);
    }
}
