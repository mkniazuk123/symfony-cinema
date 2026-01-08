<?php

namespace App\Catalog\Application\Model;

use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;

readonly class MovieDto
{
    public function __construct(
        public MovieId $id,
        public MovieStatus $status,
        public MovieDetails $details,
        public MovieLength $length,
    ) {
    }
}
