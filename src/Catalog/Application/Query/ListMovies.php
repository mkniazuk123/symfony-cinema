<?php

namespace App\Catalog\Application\Query;

use App\Catalog\Application\Model\MovieListDto;
use App\Core\Application\Query;

/**
 * @implements Query<MovieListDto>
 */
readonly class ListMovies implements Query
{
    public function __construct()
    {
    }
}
