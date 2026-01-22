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

    final public function value(): int
    {
        return $this->value;
    }

    final public function equals(Value $other): bool
    {
        return $other instanceof static && $other->value() === $this->value();
    }
}
