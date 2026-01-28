<?php

namespace App\Catalog\API\REST;

use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Core\Interfaces\ApiProblems\ApiProblem;

readonly class InvalidMovieStatusApiProblem extends ApiProblem
{
    public static function fromException(InvalidMovieStatusException $exception): self
    {
        return self::fromArray([
            'type' => 'invalidMovieStatus',
            'title' => 'Invalid movie status',
            'status' => 400,
            'movieId' => (string) $exception->movieId,
            'movieStatus' => $exception->status->value,
        ]);
    }
}
