<?php

namespace App\Core\Domain;

readonly class DateTime implements \Stringable
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

    final public function __construct(private \DateTimeImmutable $value)
    {
    }

    public function value(): \DateTimeImmutable
    {
        return $this->value;
    }

    public function isBefore(self $other): bool
    {
        return $this->value() < $other->value();
    }

    public function add(Duration $duration): self
    {
        return new self(
            $this->value->add($duration->toDateInterval())
        );
    }

    public function equals(self $other): bool
    {
        return $other->value() == $this->value();
    }

    public function __toString(): string
    {
        return $this->value->format(self::FORMAT);
    }
}
