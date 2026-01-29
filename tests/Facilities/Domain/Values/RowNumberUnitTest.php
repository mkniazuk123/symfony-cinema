<?php

namespace App\Tests\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\RowNumber;
use PHPUnit\Framework\TestCase;

class RowNumberUnitTest extends TestCase
{
    public function testCannotBeZero(): void
    {
        $this->expectException(InvalidValueException::class);
        new RowNumber(0);
    }

    public function testCannotBeNegative(): void
    {
        $this->expectException(InvalidValueException::class);
        new RowNumber(-1);
    }
}
