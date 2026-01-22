<?php

namespace App\Catalog\Application\Query\Handler;

use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Application\Query\ListMovies;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ListMoviesHandler
{
    public function __construct(
        private MovieReadModel $movieReadModel,
    ) {
    }

    public function __invoke(ListMovies $query): MovieListDto
    {
        return $this->movieReadModel->readMovies();
    }
}
