<?php

namespace App\Tests\Core\Infrastructure;

use App\Core\Domain\Clock;

class StaticClock implements Clock
{
    private \DateTimeImmutable $time;

    public function __construct(\DateTimeImmutable|string|null $time = null)
    {
        $this->setTime($time);
    }

    public function setTime(\DateTimeImmutable|string|null $time = null): void
    {
        if (is_string($time)) {
            $time = new \DateTimeImmutable($time);
        }

        $this->time = $time ?? new \DateTimeImmutable('now');
    }

    public function now(): \DateTimeImmutable
    {
        return $this->time;
    }
}
