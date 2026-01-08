<?php

namespace App\Core\Domain;

readonly class Duration
{
    public static function seconds(int $seconds): self
    {
        return new self($seconds);
    }

    public static function minutes(int $minutes): self
    {
        return new self($minutes * 60);
    }

    public function __construct(private int $seconds)
    {
    }

    public function inSeconds(): int
    {
        return $this->seconds;
    }

    public function toDateInterval(): \DateInterval
    {
        return new \DateInterval('PT'.$this->inSeconds().'S');
    }

    public function equals(self $other): bool
    {
        return $other->inSeconds() === $this->inSeconds();
    }
}
