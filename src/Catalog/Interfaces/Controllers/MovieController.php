<?php

namespace App\Catalog\Interfaces\Controllers;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Services\MovieService;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Interfaces\ApiProblems\InvalidMovieStatusApiProblem;
use App\Catalog\Interfaces\ApiProblems\MovieNotFoundApiProblem;
use App\Catalog\Interfaces\Requests\CreateMovieRequest;
use App\Catalog\Interfaces\Requests\MovieDetailsRequest;
use App\Catalog\Interfaces\Requests\MovieLengthRequest;
use App\Core\Interfaces\Controllers\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/movies')]
class MovieController extends ApiController
{
    public function __construct(
        private MovieService $service,
    ) {
    }

    #[Route(methods: ['POST'])]
    public function createMovie(#[MapRequestPayload] CreateMovieRequest $request): Response
    {
        $details = $request->details->build();
        $length = $request->length->build();

        $movieId = $this->service->createMovie($details, $length);
        $movie = $this->service->getMovie($movieId);

        return $this->jsonResponse($movie, status: 201);
    }

    #[Route(methods: ['GET'])]
    public function getMovies(): Response
    {
        $movies = $this->service->getMovies();

        return $this->jsonResponse($movies);
    }

    #[Route(path: '/{id}', methods: ['GET'])]
    public function getMovie(MovieId $id): Response
    {
        try {
            $movie = $this->service->getMovie($id);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        }

        return $this->jsonResponse($movie);
    }

    #[Route(path: '/{id}/details', methods: ['PUT'])]
    public function updateMovieDetails(
        MovieId $id,
        #[MapRequestPayload] MovieDetailsRequest $request,
    ): Response {
        $details = $request->build();

        try {
            $this->service->updateMovieDetails($id, $details);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        $movie = $this->service->getMovie($id);

        return $this->jsonResponse($movie);
    }

    #[Route(path: '/{id}/length', methods: ['PUT'])]
    public function updateMovieLength(
        MovieId $id,
        #[MapRequestPayload] MovieLengthRequest $request,
    ): Response {
        $length = $request->build();

        try {
            $this->service->updateMovieLength($id, $length);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        $movie = $this->service->getMovie($id);

        return $this->jsonResponse($movie);
    }

    #[Route(path: '/{id}/release', methods: ['POST'])]
    public function releaseMovie(MovieId $id): Response
    {
        try {
            $this->service->releaseMovie($id);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        $movie = $this->service->getMovie($id);

        return $this->jsonResponse($movie);
    }

    #[Route(path: '/{id}/archive', methods: ['POST'])]
    public function archiveMovie(MovieId $id): Response
    {
        try {
            $this->service->archiveMovie($id);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        $movie = $this->service->getMovie($id);

        return $this->jsonResponse($movie);
    }
}
