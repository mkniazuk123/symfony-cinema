<?php

namespace App\Core\Domain;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
