<?php

namespace App\Tests\Planning\Fixtures;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Core\Domain\Duration;
use App\Core\Infrastructure\NativeClock;
use App\Planning\Domain\Entities\Screening;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;

class ScreeningCreateBuilder
{
    public static function create(ScreeningId|string|null $id = null): self
    {
        if (is_string($id)) {
            $id = new ScreeningId($id);
        }

        return new self($id);
    }

    private ScreeningId $id;
    private HallId $hallId;
    private MovieId $movieId;
    private DateTimeRange $time;

    private function __construct(?ScreeningId $id = null)
    {
        $this->id = $id ?? ScreeningId::generate();
        $this->hallId = HallId::generate();
        $this->movieId = MovieId::generate();
        $this->time = DateTimeRange::startingAt(DateTime::current(new NativeClock()), Duration::minutes(rand(80, 180)));
    }

    public function withHallId(HallId $hallId): self
    {
        $this->hallId = $hallId;

        return $this;
    }

    public function withMovieId(MovieId $movieId): self
    {
        $this->movieId = $movieId;

        return $this;
    }

    public function withTime(DateTimeRange $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function build(): Screening
    {
        return Screening::create(
            $this->id,
            $this->hallId,
            $this->movieId,
            $this->time,
        );
    }
}
