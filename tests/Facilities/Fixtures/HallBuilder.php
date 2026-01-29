<?php

namespace App\Tests\Facilities\Fixtures;

class HallBuilder
{
    public static function create(): HallCreateBuilder
    {
        return HallCreateBuilder::create();
    }

    public static function reconstitute(): HallReconstituteBuilder
    {
        return HallReconstituteBuilder::create();
    }
}
