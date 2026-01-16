<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;

readonly class InvalidReservationStatusApiProblem extends ApiProblem
{
    public static function fromException(InvalidReservationStatusException $exception): self
    {
        return self::fromArray([
            'type' => 'invalidReservationStatus',
            'title' => 'Invalid reservation status',
            'status' => 400,
            'reservationId' => (string) $exception->reservationId,
            'reservationStatus' => $exception->status->value,
        ]);
    }
}
