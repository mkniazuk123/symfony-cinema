<?php

namespace App\Facilities\Interfaces\ApiProblems;

use App\Core\Interfaces\ApiProblems\ApiProblem;
use App\Facilities\Application\Exceptions\ReservationNotFoundException;

readonly class ReservationNotFoundApiProblem extends ApiProblem
{
    public static function fromException(ReservationNotFoundException $exception): self
    {
        return self::fromArray([
            'type' => 'reservationNotFound',
            'title' => 'Reservation not found',
            'status' => 404,
            'reservationId' => (string) $exception->reservationId,
        ]);
    }
}
