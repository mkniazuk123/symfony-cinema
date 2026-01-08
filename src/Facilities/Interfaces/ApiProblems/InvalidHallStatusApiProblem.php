<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;

readonly class InvalidHallStatusApiProblem extends ApiProblem
{
    public static function fromException(InvalidHallStatusException $exception): self
    {
        return self::fromArray([
            'type' => 'invalidHallStatus',
            'title' => 'Invalid hall status',
            'status' => 400,
            'hallId' => (string) $exception->hallId,
            'hallStatus' => $exception->status->value,
        ]);
    }
}
