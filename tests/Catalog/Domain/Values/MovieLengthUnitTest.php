<?php

namespace App\Tests\Catalog\Domain\Values;

use App\Catalog\Domain\Values\MovieLength;
use App\Core\Domain\InvalidValueException;
use PHPUnit\Framework\TestCase;

class MovieLengthUnitTest extends TestCase
{
    public function testCannotBeZero(): void
    {
        $this->expectException(InvalidValueException::class);
        new MovieLength(0);
    }

    public function testCannotBeLessThanZero(): void
    {
        $this->expectException(InvalidValueException::class);
        new MovieLength(-1);
    }
}
