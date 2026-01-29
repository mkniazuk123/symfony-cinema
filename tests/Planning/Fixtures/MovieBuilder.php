<?php

namespace App\Tests\Planning\Fixtures;

use App\Planning\Domain\Values\MovieId;

class MovieBuilder
{
    public static function create(MovieId|string|null $id = null): MovieCreateBuilder
    {
        return MovieCreateBuilder::create($id);
    }

    public static function reconstitute(MovieId|string|null $id = null): MovieReconstituteBuilder
    {
        return MovieReconstituteBuilder::create($id);
    }
}
