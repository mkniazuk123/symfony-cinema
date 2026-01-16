<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Domain\Exceptions\InvalidTimeException;

readonly class InvalidTimeApiProblem extends ApiProblem
{
    public static function fromException(InvalidTimeException $exception): self
    {
        return self::fromArray([
            'type' => 'invalidTime',
            'title' => 'Requested time is invalid',
            'status' => 422,
            'detail' => $exception->getMessage(),
            'time' => [
                'start' => (string) $exception->time->start,
                'end' => (string) $exception->time->end,
            ],
        ]);
    }
}
