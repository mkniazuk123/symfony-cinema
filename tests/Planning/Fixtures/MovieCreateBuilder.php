<?php

namespace App\Tests\Planning\Fixtures;

use App\Core\Domain\Duration;
use App\Planning\Domain\Entities\Movie;
use App\Planning\Domain\Values\MovieId;

class MovieCreateBuilder
{
    public static function create(MovieId|string|null $id = null): self
    {
        if (is_string($id)) {
            $id = new MovieId($id);
        }

        return new self($id);
    }

    private MovieId $id;
    private Duration $duration;
    private bool $available;

    private function __construct(?MovieId $id = null)
    {
        $this->id = $id ?? MovieId::generate();
        $this->duration = Duration::minutes(rand(80, 180));
        $this->available = true;
    }

    public function withDuration(Duration $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function unavailable(): self
    {
        $this->available = false;

        return $this;
    }

    public function build(): Movie
    {
        return Movie::create(
            $this->id,
            $this->duration,
            $this->available,
        );
    }
}
