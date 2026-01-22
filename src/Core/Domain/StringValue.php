<?php

namespace App\Core\Domain;

abstract readonly class StringValue extends Value implements \Stringable
{
    /**
     * @throws InvalidValueException
     */
    protected static function validate(string $value): void
    {
    }

    /**
     * @throws InvalidValueException
     */
    final public function __construct(private string $value)
    {
        static::validate($value);
    }

    final public function value(): string
    {
        return $this->value;
    }

    final public function __toString(): string
    {
        return $this->value;
    }

    final public function equals(Value $other): bool
    {
        return $other instanceof static && $other->value() === $this->value();
    }
}
