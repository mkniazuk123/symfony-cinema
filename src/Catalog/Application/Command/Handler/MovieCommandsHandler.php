<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\Application\Command\ArchiveMovieCommand;
use App\Catalog\Application\Command\CreateMovieCommand;
use App\Catalog\Application\Command\ReleaseMovieCommand;
use App\Catalog\Application\Command\UpdateMovieDetailsCommand;
use App\Catalog\Application\Command\UpdateMovieLengthCommand;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class MovieCommandsHandler
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function archiveMovie(ArchiveMovieCommand $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->archive();
        $this->movieRepository->save($movie);
    }

    #[AsMessageHandler]
    public function createMovie(CreateMovieCommand $command): void
    {
        $movie = Movie::create($command->id, $command->details, $command->length);
        $this->movieRepository->save($movie);
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    #[AsMessageHandler]
    public function releaseMovie(ReleaseMovieCommand $command): void
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
    public function updateMovieDetails(UpdateMovieDetailsCommand $command): void
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
    public function updateMovieLength(UpdateMovieLengthCommand $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->updateLength($command->length);
        $this->movieRepository->save($movie);
    }
}
