<?php

namespace App\Facilities\Interfaces\Controllers;

use App\Core\Interfaces\Controllers\ApiController;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\ReservationNotFoundException;
use App\Facilities\Application\Services\ReservationService;
use App\Facilities\Domain\Exceptions\HallClosedException;
use App\Facilities\Domain\Exceptions\InvalidReservationStatusException;
use App\Facilities\Domain\Exceptions\InvalidTimeException;
use App\Facilities\Domain\Exceptions\UnavailableTimeException;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\ReservationId;
use App\Facilities\Interfaces\ApiProblems\HallClosedApiProblem;
use App\Facilities\Interfaces\ApiProblems\HallNotFoundApiProblem;
use App\Facilities\Interfaces\ApiProblems\InvalidReservationStatusApiProblem;
use App\Facilities\Interfaces\ApiProblems\InvalidTimeApiProblem;
use App\Facilities\Interfaces\ApiProblems\ReservationNotFoundApiProblem;
use App\Facilities\Interfaces\ApiProblems\UnavailableTimeApiProblem;
use App\Facilities\Interfaces\Requests\CreateReservationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class ReservationController extends ApiController
{
    public function __construct(private ReservationService $service)
    {
    }

    #[Route(path: '/halls/{hallId}/reservations', methods: ['POST'])]
    public function createReservation(
        HallId $hallId,
        #[MapRequestPayload] CreateReservationRequest $request,
    ): Response {
        $data = $request->resolve();
        $time = $data['time'];

        try {
            $reservationId = $this->service->createReservation($hallId, $time);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (HallClosedException $exception) {
            $this->apiProblem(HallClosedApiProblem::fromException($exception));
        } catch (InvalidTimeException $exception) {
            $this->apiProblem(InvalidTimeApiProblem::fromException($exception));
        } catch (UnavailableTimeException $exception) {
            $this->apiProblem(UnavailableTimeApiProblem::fromException($exception));
        }

        $reservation = $this->service->getReservation($reservationId);

        return $this->jsonResponse($reservation, status: 201);
    }

    #[Route(path: '/reservations', methods: ['GET'])]
    public function getReservations(): Response
    {
        $reservations = $this->service->getReservations();

        return $this->jsonResponse($reservations);
    }

    #[Route(path: '/reservations/{reservationId}', methods: ['GET'])]
    public function getReservation(ReservationId $reservationId): Response
    {
        try {
            $reservation = $this->service->getReservation($reservationId);
        } catch (ReservationNotFoundException $exception) {
            $this->apiProblem(ReservationNotFoundApiProblem::fromException($exception));
        }

        return $this->jsonResponse($reservation);
    }

    #[Route(path: '/reservations/{reservationId}/cancel', methods: ['POST'])]
    public function cancelReservation(ReservationId $reservationId): Response
    {
        try {
            $this->service->cancelReservation($reservationId);
        } catch (ReservationNotFoundException $exception) {
            $this->apiProblem(ReservationNotFoundApiProblem::fromException($exception));
        } catch (InvalidReservationStatusException $exception) {
            $this->apiProblem(InvalidReservationStatusApiProblem::fromException($exception));
        } catch (InvalidTimeException $exception) {
            $this->apiProblem(InvalidTimeApiProblem::fromException($exception));
        }

        $reservation = $this->service->getReservation($reservationId);

        return $this->jsonResponse($reservation);
    }
}
