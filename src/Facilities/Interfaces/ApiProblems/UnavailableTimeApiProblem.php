<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;

readonly class UnavailableTimeApiProblem extends ApiProblem
{
    public static function fromException(UnavailableTimeException $exception): self
    {
        return self::fromArray([
            'type' => 'unavailableTime',
            'title' => 'Requested time is unavailable',
            'status' => 422,
            'hallId' => (string) $exception->hallId,
            'time' => [
                'start' => (string) $exception->time->start,
                'end' => (string) $exception->time->end,
            ],
        ]);
    }
}
