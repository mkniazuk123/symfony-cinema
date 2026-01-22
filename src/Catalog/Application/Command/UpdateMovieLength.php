<?php

namespace App\Catalog\Application\Command;

use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Core\Application\Command;

readonly class UpdateMovieLength implements Command
{
    public function __construct(
        public MovieId $id,
        public MovieLength $length,
    ) {
    }
}
