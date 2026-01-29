<?php

namespace App\Planning\Domain\Entities;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Events\ScreeningCreated;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;

/**
 * @extends AggregateRoot<ScreeningId>
 */
class Screening extends AggregateRoot
{
    public static function create(
        ScreeningId $id,
        HallId $hallId,
        MovieId $movieId,
        DateTimeRange $time,
    ): self {
        return new self($id, $hallId, $movieId, $time, created: true);
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
        ScreeningId $id,
        private HallId $hallId,
        private MovieId $movieId,
        private DateTimeRange $time,
        bool $created = false,
    ) {
        parent::__construct($id);

        if ($created) {
            $this->recordEvent(new ScreeningCreated($id, $hallId, $movieId, $time));
        }
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
