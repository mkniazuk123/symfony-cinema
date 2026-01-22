<?php

namespace App\Planning\Domain\Exceptions;

use App\Planning\Domain\Values\MovieId;

class MovieUnavailableException extends \RuntimeException
{
    public function __construct(
        public readonly MovieId $movieId,
    ) {
        parent::__construct("The movie $movieId is unavailable");
    }
}
