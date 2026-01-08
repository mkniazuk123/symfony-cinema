<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Application\Exceptions\HallNotFoundException;

readonly class HallNotFoundApiProblem extends ApiProblem
{
    public static function fromException(HallNotFoundException $exception): self
    {
        return self::fromArray([
            'type' => 'hallNotFound',
            'title' => 'Hall not found',
            'status' => 404,
            'hallId' => (string) $exception->hallId,
        ]);
    }
}
