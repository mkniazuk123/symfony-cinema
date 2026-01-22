<?php

namespace App\Catalog\Interfaces\Controllers;

use App\Catalog\Application\Command\ArchiveMovie;
use App\Catalog\Application\Command\CreateMovie;
use App\Catalog\Application\Command\ReleaseMovie;
use App\Catalog\Application\Command\UpdateMovieDetails;
use App\Catalog\Application\Command\UpdateMovieLength;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Query\GetMovie;
use App\Catalog\Application\Query\ListMovies;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Interfaces\ApiProblems\InvalidMovieStatusApiProblem;
use App\Catalog\Interfaces\ApiProblems\MovieNotFoundApiProblem;
use App\Catalog\Interfaces\Requests\CreateMovieRequest;
use App\Catalog\Interfaces\Requests\MovieDetailsRequest;
use App\Catalog\Interfaces\Requests\MovieLengthRequest;
use App\Core\Application\CommandBus;
use App\Core\Application\QueryBus;
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
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    #[Route(methods: ['POST'])]
    public function createMovie(#[MapRequestPayload] CreateMovieRequest $request): Response
    {
        $id = MovieId::generate();
        $details = $request->details->build();
        $length = $request->length->build();

        $this->commandBus->dispatch(new CreateMovie($id, $details, $length));

        return $this->sendMovie($id, status: 201);
    }

    #[Route(methods: ['GET'])]
    public function getMovies(): Response
    {
        $movies = $this->queryBus->query(new ListMovies());

        return $this->jsonResponse($movies);
    }

    #[Route(path: '/{id}', methods: ['GET'])]
    public function getMovie(MovieId $id): Response
    {
        try {
            return $this->sendMovie($id);
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        }
    }

    #[Route(path: '/{id}/details', methods: ['PUT'])]
    public function updateMovieDetails(
        MovieId $id,
        #[MapRequestPayload] MovieDetailsRequest $request,
    ): Response {
        $details = $request->build();

        try {
            $this->commandBus->dispatch(new UpdateMovieDetails($id, $details));
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        return $this->sendMovie($id);
    }

    #[Route(path: '/{id}/length', methods: ['PUT'])]
    public function updateMovieLength(
        MovieId $id,
        #[MapRequestPayload] MovieLengthRequest $request,
    ): Response {
        $length = $request->build();

        try {
            $this->commandBus->dispatch(new UpdateMovieLength($id, $length));
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        return $this->sendMovie($id);
    }

    #[Route(path: '/{id}/release', methods: ['POST'])]
    public function releaseMovie(MovieId $id): Response
    {
        try {
            $this->commandBus->dispatch(new ReleaseMovie($id));
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        return $this->sendMovie($id);
    }

    #[Route(path: '/{id}/archive', methods: ['POST'])]
    public function archiveMovie(MovieId $id): Response
    {
        try {
            $this->commandBus->dispatch(new ArchiveMovie($id));
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        return $this->sendMovie($id);
    }

    private function sendMovie(MovieId $id, int $status = 200): Response
    {
        $movie = $this->queryBus->query(new GetMovie($id));

        return $this->jsonResponse($movie, status: $status);
    }
}
