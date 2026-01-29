<?php

namespace App\Tests\Planning\Fixtures;

use App\Planning\Domain\Values\HallId;

class HallBuilder
{
    public static function create(HallId|string|null $id = null): HallCreateBuilder
    {
        return HallCreateBuilder::create($id);
    }

    public static function reconstitute(HallId|string|null $id = null): HallReconstituteBuilder
    {
        return HallReconstituteBuilder::create($id);
    }
}
