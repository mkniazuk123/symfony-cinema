<?php

namespace App\Planning\Domain\Entities;

use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;

class Screening
{
    public static function create(
        ScreeningId $id,
        HallId $hallId,
        MovieId $movieId,
        DateTimeRange $time,
    ): self {
        return new self($id, $hallId, $movieId, $time);
    }

    public static function reconstitute(
        ScreeningId $id,
        HallId $hallId,
        MovieId $movieId,
        DateTimeRange $time,
    ): self {
        return new self($id, $hallId, $movieId, $time);
    }

    private function __construct(
        private ScreeningId $id,
        private HallId $hallId,
        private MovieId $movieId,
        private DateTimeRange $time,
    ) {
    }

    public function getId(): ScreeningId
    {
        return $this->id;
    }

    public function getHallId(): HallId
    {
        return $this->hallId;
    }

    public function getMovieId(): MovieId
    {
        return $this->movieId;
    }

    public function getTime(): DateTimeRange
    {
        return $this->time;
    }
}
