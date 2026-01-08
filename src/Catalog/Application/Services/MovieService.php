<?php

namespace App\Catalog\Application\Services;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use Doctrine\DBAL\Connection;

class MovieService
{
    public function __construct(
        private Connection $connection,
        private MovieRepository $movieRepository,
        private MovieReadModel $movieReadModel,
    ) {
    }

    /**
     * @throws MovieNotFoundException
     */
    public function getMovie(MovieId $id): MovieDto
    {
        return $this->movieReadModel->readMovie($id) ?? throw new MovieNotFoundException($id);
    }

    public function getMovies(): MovieListDto
    {
        return $this->movieReadModel->readMovies();
    }

    public function createMovie(MovieDetails $details, MovieLength $length): MovieId
    {
        return $this->connection->transactional(function () use ($details, $length) {
            $movie = Movie::create($details, $length);
            $this->movieRepository->save($movie);

            return $movie->getId();
        });
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function updateMovieDetails(MovieId $id, MovieDetails $details): void
    {
        $this->connection->transactional(function () use ($id, $details) {
            $movie = $this->movieRepository->find($id) ?? throw new MovieNotFoundException($id);
            $movie->updateDetails($details);
            $this->movieRepository->save($movie);
        });
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function updateMovieLength(MovieId $id, MovieLength $length): void
    {
        $this->connection->transactional(function () use ($id, $length) {
            $movie = $this->movieRepository->find($id) ?? throw new MovieNotFoundException($id);
            $movie->updateLength($length);
            $this->movieRepository->save($movie);
        });
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function releaseMovie(MovieId $id): void
    {
        $this->connection->transactional(function () use ($id) {
            $movie = $this->movieRepository->find($id) ?? throw new MovieNotFoundException($id);
            $movie->release();
            $this->movieRepository->save($movie);
        });
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function archiveMovie(MovieId $id): void
    {
        $this->connection->transactional(function () use ($id) {
            $movie = $this->movieRepository->find($id) ?? throw new MovieNotFoundException($id);
            $movie->archive();
            $this->movieRepository->save($movie);
        });
    }
}
