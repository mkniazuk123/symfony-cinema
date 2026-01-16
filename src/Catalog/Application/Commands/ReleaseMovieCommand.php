<?php

namespace App\Catalog\Application\Commands;

use App\Catalog\Domain\Values\MovieId;
use App\Core\Application\Command;

readonly class ReleaseMovieCommand implements Command
{
    public function __construct(
        public MovieId $id,
    ) {
    }
}
