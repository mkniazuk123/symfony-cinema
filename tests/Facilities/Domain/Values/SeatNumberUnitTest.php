<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatNumber;
use PHPUnit\Framework\TestCase;

class SeatNumberUnitTest extends TestCase
{
    public function testCannotBeZero(): void
    {
        $this->expectException(InvalidValueException::class);
        new SeatNumber(0);
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(InvalidValueException::class);
        new SeatNumber(-1);
    }
}
