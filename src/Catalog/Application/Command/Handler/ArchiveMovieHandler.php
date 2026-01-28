<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\API\Events\MovieArchived;
use App\Catalog\Application\Command\ArchiveMovie;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Core\Application\IntegrationBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ArchiveMovieHandler
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
    public function __invoke(ArchiveMovie $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->archive();
        $this->movieRepository->save($movie);
        $this->integrationBus->dispatch(new MovieArchived($command->id->value()));
    }
}
