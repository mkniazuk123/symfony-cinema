<?php

namespace App\Core\Domain;

final readonly class Duration extends Value
{
    public static function seconds(int $seconds): self
    {
        return new self($seconds);
    }

    public static function minutes(int $minutes): self
    {
        return new self($minutes * 60);
    }

    public static function hours(int $hours): self
    {
        return new self($hours * 3600);
    }

    public static function days(int $days): self
    {
        return new self($days * 86400);
    }

    public function __construct(private int $seconds)
    {
        if ($seconds < 0) {
            throw new \InvalidArgumentException('Duration cannot be negative.');
        }
    }

    public function inSeconds(): int
    {
        return $this->seconds;
    }

    public function toDateInterval(): \DateInterval
    {
        return new \DateInterval('PT'.$this->inSeconds().'S');
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->inSeconds() === $this->inSeconds();
    }
}
