<?php

namespace App\Tests\Catalog\Fixtures;

use App\Catalog\Domain\Entities\Movie;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;

class MovieBuilder
{
    private MovieId $id;
    private MovieStatus $status;
    private MovieDetails $details;
    private MovieLength $length;

    public function __construct(?MovieId $id = null)
    {
        $this->id = $id ?? MovieId::generate();
        $this->details = new MovieDetailsBuilder()->build();
        $this->length = new MovieLength(120);
        $this->status = MovieStatus::UPCOMING;
    }

    public function withStatus(MovieStatus|string $status): self
    {
        if (is_string($status)) {
            $status = MovieStatus::from($status);
        }
        $this->status = $status;

        return $this;
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
        return Movie::reconstitute(
            $this->id,
            $this->status,
            $this->details,
            $this->length,
        );
    }
}
