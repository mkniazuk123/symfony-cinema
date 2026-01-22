<?php

namespace App\Tests\Core\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\Duration;
use App\Core\Infrastructure\NativeClock;

class FutureDateTimeBuilder
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

    public function inSeconds(?int $seconds = null): self
    {
        $seconds ??= rand(1, 59);
        $this->duration = Duration::seconds($seconds);

        return $this;
    }

    public function inMinutes(?int $minutes = null): self
    {
        $minutes ??= rand(1, 59);
        $this->duration = Duration::minutes($minutes);

        return $this;
    }

    public function inHours(?int $hours = null): self
    {
        $hours ??= rand(1, 23);
        $this->duration = Duration::hours($hours);

        return $this;
    }

    public function inDays(?int $days = null): self
    {
        $days ??= rand(1, 30);
        $this->duration = Duration::days($days);

        return $this;
    }

    public function build(): DateTime
    {
        return DateTime::current(new NativeClock())->add($this->duration);
    }
}
