<?php

namespace App\Planning\Application\Exceptions;

use App\Planning\Domain\Values\MovieId;

class MovieNotFoundException extends \RuntimeException
{
    public function __construct(
        public readonly MovieId $movieId,
    ) {
        parent::__construct("Movie $movieId not found.");
    }
}
