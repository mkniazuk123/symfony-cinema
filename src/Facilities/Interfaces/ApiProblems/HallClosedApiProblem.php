<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Domain\Exceptions\HallClosedException;

readonly class HallClosedApiProblem extends ApiProblem
{
    public static function fromException(HallClosedException $exception): self
    {
        return self::fromArray([
            'type' => 'hallClosed',
            'title' => 'Hall is closed',
            'status' => 400,
            'hallId' => (string) $exception->hallId,
        ]);
    }
}
