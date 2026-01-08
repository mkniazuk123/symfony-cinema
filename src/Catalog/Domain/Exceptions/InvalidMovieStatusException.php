<?php

namespace App\Catalog\Domain\Exceptions;

use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieStatus;

class InvalidMovieStatusException extends \RuntimeException
{
    public function __construct(
        public readonly MovieId $movieId,
        public readonly MovieStatus $status,
    ) {
        parent::__construct("Invalid status $status->value for movie $movieId");
    }
}
