<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\API\Events\MovieReleased;
use App\Catalog\Application\Command\ReleaseMovie;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Core\Application\IntegrationBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReleaseMovieHandler
{
    public function __construct(
        private MovieRepository $movieRepository,
        private IntegrationBus $integrationBus,
    ) {
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
        $this->integrationBus->dispatch(new MovieReleased($command->id->value()));
    }
}
