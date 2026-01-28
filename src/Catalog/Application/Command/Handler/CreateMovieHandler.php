<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\API\Events\MovieCreated;
use App\Catalog\API\Model\MovieDetails;
use App\Catalog\API\Model\MovieLength;
use App\Catalog\Application\Command\CreateMovie;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Ports\MovieRepository;
use App\Core\Application\IntegrationBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMovieHandler
{
    public function __construct(
        private MovieRepository $movieRepository,
        private IntegrationBus $integrationBus,
    ) {
    }

    public function __invoke(CreateMovie $command): void
    {
        $movie = Movie::create($command->id, $command->details, $command->length);
        $this->movieRepository->save($movie);
        $this->integrationBus->dispatch(
            new MovieCreated(
                id: $command->id->value(),
                status: $movie->getStatus()->value,
                details: new MovieDetails(
                    title: $movie->getDetails()->title,
                    description: $movie->getDetails()->description,
                ),
                length: new MovieLength(
                    minutes: $movie->getLength()->minutes,
                )
            )
        );
    }
}
