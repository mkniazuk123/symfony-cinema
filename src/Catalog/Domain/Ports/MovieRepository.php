<?php

namespace App\Catalog\Domain\Ports;

use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Values\MovieId;

interface MovieRepository
{
    public function find(MovieId $id): ?Movie;

    public function save(Movie $movie): void;
}
