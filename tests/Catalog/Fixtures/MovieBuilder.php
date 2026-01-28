<?php

namespace App\Tests\Catalog\Fixtures;

use App\Catalog\Domain\Values\MovieId;

class MovieBuilder
{
    public static function create(?MovieId $id = null): MovieCreateBuilder
    {
        return MovieCreateBuilder::create($id);
    }

    public static function reconstitute(?MovieId $id = null): MovieReconstituteBuilder
    {
        return MovieReconstituteBuilder::create($id);
    }
}
