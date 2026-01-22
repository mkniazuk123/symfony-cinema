<?php

namespace App\Core\Domain;

final readonly class DateTime extends Value implements \Stringable
{
    private const FORMAT = 'Y-m-d\TH:i:sP';

    public static function current(Clock $clock): static
    {
        return new static($clock->now());
    }

    public static function parse(string $string): static
    {
        $value = new \DateTimeImmutable($string);

        return new static($value);
    }

    private \DateTimeImmutable $value;

    final public function __construct(\DateTimeImmutable $value)
    {
        $this->value = $this->trimValueToSeconds($value);
    }

    public function value(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function isBefore(self $other): bool
    {
        return $this->value() < $other->value();
    }

    public function isAfter(self $other): bool
    {
        return $this->value() > $other->value();
    }

    public function isFuture(Clock $clock): bool
    {
        return $this->isAfter(self::current($clock));
    }

    public function add(Duration $duration): self
    {
        return new self(
            $this->value->add($duration->toDateInterval())
        );
    }

    public function subtract(Duration $duration): self
    {
        return new self(
            $this->value->sub($duration->toDateInterval())
        );
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->value() == $this->value();
    }

    public function __toString(): string
    {
        return $this->value->format(self::FORMAT);
    }

    private function trimValueToSeconds(\DateTimeImmutable $value): \DateTimeImmutable
    {
        return $value->setTime(
            hour: (int) $value->format('H'),
            minute: (int) $value->format('i'),
            second: (int) $value->format('s'),
        );
    }
}
