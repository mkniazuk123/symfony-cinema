<?php

namespace App\Catalog\Application\Services;

use App\Catalog\Application\Exceptions\MovieNotFoundException;
use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Application\Ports\MovieReadModel;
use App\Catalog\Domain\Values\MovieId;

class MovieService
{
    public function __construct(
        private MovieReadModel $movieReadModel,
    ) {
    }

    /**
     * @throws MovieNotFoundException
     */
    public function getMovie(MovieId $id): MovieDto
    {
        return $this->movieReadModel->readMovie($id) ?? throw new MovieNotFoundException($id);
    }

    public function getMovies(): MovieListDto
    {
        return $this->movieReadModel->readMovies();
    }
}
