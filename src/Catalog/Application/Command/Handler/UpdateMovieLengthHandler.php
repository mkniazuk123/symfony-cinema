<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\API\Events\MovieLengthUpdated;
use App\Catalog\API\Model\MovieLength;
use App\Catalog\Application\Command\UpdateMovieLength;
use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Core\Application\IntegrationBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMovieLengthHandler
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
    public function __invoke(UpdateMovieLength $command): void
    {
        $movie = $this->movieRepository->find($command->id) ?? throw new MovieNotFoundException($command->id);
        $movie->updateLength($command->length);
        $this->movieRepository->save($movie);
        $this->integrationBus->dispatch(
            new MovieLengthUpdated(
                id: $command->id->value(),
                length: new MovieLength(
                    minutes: $movie->getLength()->minutes,
                )
            )
        );
    }
}
