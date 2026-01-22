<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\Application\Command\ArchiveMovie;
use App\Catalog\Application\Command\CreateMovie;
use App\Catalog\Application\Command\ReleaseMovie;
use App\Catalog\Application\Command\UpdateMovieDetails;
use App\Catalog\Application\Command\UpdateMovieLength;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class MovieHandler
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function archiveMovie(ArchiveMovie $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->archive();
        $this->movieRepository->save($movie);
    }

    #[AsMessageHandler]
    public function createMovie(CreateMovie $command): void
    {
        $movie = Movie::create($command->id, $command->details, $command->length);
        $this->movieRepository->save($movie);
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function releaseMovie(ReleaseMovie $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->release();
        $this->movieRepository->save($movie);
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function updateMovieDetails(UpdateMovieDetails $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->updateDetails($command->details);
        $this->movieRepository->save($movie);
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function updateMovieLength(UpdateMovieLength $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->updateLength($command->length);
        $this->movieRepository->save($movie);
    }
}
