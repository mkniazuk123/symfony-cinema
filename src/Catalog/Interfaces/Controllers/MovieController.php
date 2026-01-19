<?php

namespace App\Catalog\Interfaces\Controllers;

use App\Catalog\Application\Command\ArchiveMovieCommand;
use App\Catalog\Application\Command\CreateMovieCommand;
use App\Catalog\Application\Command\ReleaseMovieCommand;
use App\Catalog\Application\Command\UpdateMovieDetailsCommand;
use App\Catalog\Application\Command\UpdateMovieLengthCommand;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Query\GetMovieQuery;
use App\Catalog\Application\Query\ListMoviesQuery;
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

        $this->commandBus->dispatch(new CreateMovieCommand($id, $details, $length));

        return $this->sendMovie($id, status: 201);
    }

    #[Route(methods: ['GET'])]
    public function getMovies(): Response
    {
        $movies = $this->queryBus->query(new ListMoviesQuery());

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
            $this->commandBus->dispatch(new UpdateMovieDetailsCommand($id, $details));
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
            $this->commandBus->dispatch(new UpdateMovieLengthCommand($id, $length));
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
            $this->commandBus->dispatch(new ReleaseMovieCommand($id));
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
            $this->commandBus->dispatch(new ArchiveMovieCommand($id));
        } catch (MovieNotFoundException $exception) {
            $this->apiProblem(MovieNotFoundApiProblem::fromException($exception));
        } catch (InvalidMovieStatusException $exception) {
            $this->apiProblem(InvalidMovieStatusApiProblem::fromException($exception));
        }

        return $this->sendMovie($id);
    }

    private function sendMovie(MovieId $id, int $status = 200): Response
    {
        $movie = $this->queryBus->query(new GetMovieQuery($id));

        return $this->jsonResponse($movie, status: $status);
    }
}
