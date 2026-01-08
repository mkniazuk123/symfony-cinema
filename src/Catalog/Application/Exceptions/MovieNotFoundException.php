<?php

namespace App\Catalog\Application\Exceptions;

use App\Catalog\Domain\Values\MovieId;

class MovieNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly MovieId $movieId,
    ) {
        parent::__construct("Movie $movieId not found.");
    }
}
