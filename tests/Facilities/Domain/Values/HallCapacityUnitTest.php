<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\HallCapacity;
use PHPUnit\Framework\TestCase;

class HallCapacityUnitTest extends TestCase
{
    public function testCannotBeZero(): void
    {
        $this->expectException(InvalidValueException::class);
        new HallCapacity(0);
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(InvalidValueException::class);
        new HallCapacity(-1);
    }
}
