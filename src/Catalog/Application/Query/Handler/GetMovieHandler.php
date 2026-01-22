<?php

namespace App\Catalog\Application\Query\Handler;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Application\Query\GetMovie;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetMovieHandler
{
    public function __construct(
        private MovieReadModel $movieReadModel,
    ) {
    }

    /**
     * @throws MovieNotFoundException
     */
    public function __invoke(GetMovie $query): ?MovieDto
    {
        return $this->movieReadModel->readMovie($query->id) ?? throw new MovieNotFoundException($query->id);
    }
}
