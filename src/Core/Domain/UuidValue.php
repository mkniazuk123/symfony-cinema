<?php

namespace App\Core\Domain;

use Symfony\Component\Uid\Uuid;

abstract readonly class UuidValue extends StringValue
{
    public static function generate(): static
    {
        return new static(Uuid::v4()->toBase58());
    }
}
