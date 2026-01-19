<?php

namespace App\Catalog\Application\Query\Handler;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Application\Query\GetMovieQuery;
use App\Catalog\Application\Query\ListMoviesQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class MovieQueryHandler
{
    public function __construct(
        private MovieReadModel $movieReadModel,
    ) {
    }

    /**
     * @throws MovieNotFoundException
     */
    #[AsMessageHandler]
    public function getMovie(GetMovieQuery $query): ?MovieDto
    {
        return $this->movieReadModel->readMovie($query->id) ?? throw new MovieNotFoundException($query->id);
    }

    #[AsMessageHandler]
    public function listMovies(ListMoviesQuery $query): MovieListDto
    {
        return $this->movieReadModel->readMovies();
    }
}
