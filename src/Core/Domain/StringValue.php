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

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->value() === $this->value();
    }
}
