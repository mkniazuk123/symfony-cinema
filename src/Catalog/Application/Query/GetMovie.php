<?php

namespace App\Catalog\Application\Query;

use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Domain\Values\MovieId;
use App\Core\Application\Query;

/**
 * @implements Query<MovieDto>
 */
readonly class GetMovie implements Query
{
    public function __construct(public MovieId $id)
    {
    }
}
