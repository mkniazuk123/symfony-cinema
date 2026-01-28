<?php

namespace App\Tests\Catalog\Fixtures;

use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;

class MovieCreateBuilder
{
    public static function create(?MovieId $id = null): self
    {
        return new self($id);
    }

    private MovieId $id;
    private MovieDetails $details;
    private MovieLength $length;

    private function __construct(?MovieId $id = null)
    {
        $this->id = $id ?? MovieId::generate();
        $this->details = MovieDetailsBuilder::create()->build();
        $this->length = new MovieLength(120);
    }

    public function withDetails(MovieDetails $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function withLength(MovieLength|int $length): self
    {
        if (is_int($length)) {
            $length = new MovieLength($length);
        }
        $this->length = $length;

        return $this;
    }

    public function build(): Movie
    {
        return Movie::create(
            $this->id,
            $this->details,
            $this->length,
        );
    }
}
