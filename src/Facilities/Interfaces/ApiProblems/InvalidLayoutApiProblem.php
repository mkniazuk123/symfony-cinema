<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Application\Exceptions\InvalidLayoutException;

readonly class InvalidLayoutApiProblem extends ApiProblem
{
    public static function fromException(InvalidLayoutException $exception): self
    {
        return self::fromArray([
            'type' => 'invalidLayout',
            'title' => 'Invalid layout',
            'status' => 422,
            'detail' => $exception->getMessage(),
        ]);
    }
}
