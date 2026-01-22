<?php

namespace App\Tests\Core\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\Duration;
use App\Core\Infrastructure\NativeClock;

class PastDateTimeBuilder
{
    public static function create(): self
    {
        return new self();
    }

    private Duration $duration;

    private function __construct()
    {
        $this->duration = Duration::seconds(rand(1, 86400 * 365));
    }

    public function secondsAgo(?int $seconds = null): self
    {
        $seconds ??= rand(1, 59);
        $this->duration = Duration::seconds($seconds);

        return $this;
    }

    public function minutesAgo(?int $minutes = null): self
    {
        $minutes ??= rand(1, 59);
        $this->duration = Duration::minutes($minutes);

        return $this;
    }

    public function hoursAgo(?int $hours = null): self
    {
        $hours ??= rand(1, 23);
        $this->duration = Duration::hours($hours);

        return $this;
    }

    public function daysAgo(?int $days = null): self
    {
        $days ??= rand(1, 30);
        $this->duration = Duration::days($days);

        return $this;
    }

    public function build(): DateTime
    {
        return DateTime::current(new NativeClock())->subtract($this->duration);
    }
}
