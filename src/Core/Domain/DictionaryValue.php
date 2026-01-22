<?php

namespace App\Core\Domain;

abstract readonly class DictionaryValue extends Value
{
    /** @var array<string, scalar> */
    private array $data;

    /**
     * @param array<string, scalar> $data
     *
     * @throws InvalidValueException
     */
    final public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (!is_scalar($value)) {
                throw new InvalidValueException(sprintf('Invalid value for key "%s": expected scalar, got %s.', $key, gettype($value)));
            }
        }

        ksort($data);
        $this->data = $data;
    }

    final public function getData(): array
    {
        return $this->data;
    }

    final public function equals(Value $other): bool
    {
        return $other instanceof static && $other->data === $this->data;
    }
}
