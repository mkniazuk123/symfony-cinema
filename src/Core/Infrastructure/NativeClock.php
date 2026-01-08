<?php

namespace App\Core\Infrastructure;

use App\Core\Domain\Clock;

class NativeClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now');
    }
}
