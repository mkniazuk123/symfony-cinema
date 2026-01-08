<?php

namespace App\Catalog\Interfaces\ApiProblems;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Core\Interfaces\ApiProblems\ApiProblem;

readonly class MovieNotFoundApiProblem extends ApiProblem
{
    public static function fromException(MovieNotFoundException $exception): self
    {
        return self::fromArray([
            'type' => 'movieNotFound',
            'title' => 'Movie not found',
            'status' => 404,
            'movieId' => (string) $exception->movieId,
        ]);
    }
}
