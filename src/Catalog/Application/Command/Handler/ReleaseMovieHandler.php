<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\Application\Command\ReleaseMovie;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReleaseMovieHandler
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function __invoke(ReleaseMovie $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->release();
        $this->movieRepository->save($movie);
    }
}
