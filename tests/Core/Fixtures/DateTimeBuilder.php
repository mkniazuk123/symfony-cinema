<?php

namespace App\Tests\Core\Fixtures;

class DateTimeBuilder
{
    public static function past(): PastDateTimeBuilder
    {
        return PastDateTimeBuilder::create();
    }

    public static function future(): FutureDateTimeBuilder
    {
        return FutureDateTimeBuilder::create();
    }
}
