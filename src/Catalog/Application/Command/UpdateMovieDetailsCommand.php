<?php

namespace App\Catalog\Application\Command;

use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Core\Application\Command;

readonly class UpdateMovieDetailsCommand implements Command
{
    public function __construct(
        public MovieId $id,
        public MovieDetails $details,
    ) {
    }
}
