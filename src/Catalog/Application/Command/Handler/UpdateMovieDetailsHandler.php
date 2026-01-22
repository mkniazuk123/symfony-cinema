<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\Application\Command\UpdateMovieDetails;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMovieDetailsHandler
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    /**
     * @throws MovieNotFoundException
     * @throws InvalidMovieStatusException
     */
    public function __invoke(UpdateMovieDetails $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->updateDetails($command->details);
        $this->movieRepository->save($movie);
    }
}
