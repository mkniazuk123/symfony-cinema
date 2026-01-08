<?php

namespace App\Core\Domain;

abstract readonly class IntegerValue
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

    public function equals(self $other): bool
    {
        return $other->value() === $this->value();
    }
}
