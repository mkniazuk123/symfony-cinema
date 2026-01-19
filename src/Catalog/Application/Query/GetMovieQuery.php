<?php

namespace App\Catalog\Application\Query;

use App\Catalog\Domain\Values\MovieId;
use App\Core\Application\Query;

readonly class GetMovieQuery implements Query
{
    public function __construct(public MovieId $id)
    {
    }
}
