<?php

namespace App\Core\Domain;

abstract readonly class IntegerValue extends Value
{
    /**
     * @throws InvalidValueException
     */
    protected static function validate(int $value): void
    {
    }

    /**
     * @throws InvalidValueException
     */
    final public function __construct(private int $value)
    {
        static::validate($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->value() === $this->value();
    }
}
