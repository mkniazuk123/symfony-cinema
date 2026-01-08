<?php

namespace App\Catalog\Application\Ports;

use App\Catalog\Application\Model\MovieDto;
use App\Catalog\Application\Model\MovieListDto;
use App\Catalog\Domain\Values\MovieId;

interface MovieReadModel
{
    public function readMovie(MovieId $id): ?MovieDto;

    public function readMovies(): MovieListDto;
}
