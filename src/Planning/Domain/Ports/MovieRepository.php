<?php

namespace App\Planning\Domain\Ports;

use App\Planning\Domain\Entities\Movie;
use App\Planning\Domain\Values\MovieId;

interface MovieRepository
{
    public function find(MovieId $id): ?Movie;

    public function save(Movie $movie): void;
}
