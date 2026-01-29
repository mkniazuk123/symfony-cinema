<?php

namespace App\Tests\Planning\Fixtures;

use App\Planning\Domain\Values\ScreeningId;

class ScreeningBuilder
{
    public static function create(ScreeningId|string|null $id = null): ScreeningCreateBuilder
    {
        return ScreeningCreateBuilder::create($id);
    }

    public static function reconstitute(ScreeningId|string|null $id = null): ScreeningReconsituteBuilder
    {
        return ScreeningReconsituteBuilder::create($id);
    }
}
