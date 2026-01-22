<?php

namespace App\Catalog\Application\Command\Handler;

use App\Catalog\Application\Command\CreateMovie;
use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Ports\MovieRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMovieHandler
{
    public function __construct(private MovieRepository $movieRepository)
    {
    }

    public function __invoke(CreateMovie $command): void
    {
        $movie = Movie::create($command->id, $command->details, $command->length);
        $this->movieRepository->save($movie);
    }
}
