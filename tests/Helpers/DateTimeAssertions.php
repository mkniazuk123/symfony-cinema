<?php

namespace App\Tests\Helpers;

use App\Core\Domain\DateTime;
use PHPUnit\Framework\Assert;

trait DateTimeAssertions
{
    public function assertDateTimeEquals(DateTime|string $expected, DateTime|string $actual, string $message = ''): void
    {
        if (is_string($expected)) {
            $expected = DateTime::parse($expected);
        }
        if (is_string($actual)) {
            $actual = DateTime::parse($actual);
        }

        Assert::assertEquals(
            $expected->value(),
            $actual->value(),
            $message
        );
    }
}
