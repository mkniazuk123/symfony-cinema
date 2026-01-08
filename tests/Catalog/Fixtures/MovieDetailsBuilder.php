<?php

namespace App\Tests\Catalog\Fixtures;

use App\Catalog\Domain\Values\MovieDescription;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieTitle;

class MovieDetailsBuilder
{
    private MovieTitle $title;
    private MovieDescription $description;

    public function __construct()
    {
        $this->title = new MovieTitle('Default Title');
        $this->description = new MovieDescription('Default Description');
    }

    public function withTitle(MovieTitle|string $title): self
    {
        if (is_string($title)) {
            $title = new MovieTitle($title);
        }
        $this->title = $title;

        return $this;
    }

    public function withDescription(MovieDescription|string $description): self
    {
        if (is_string($description)) {
            $description = new MovieDescription($description);
        }
        $this->description = $description;

        return $this;
    }

    public function build(): MovieDetails
    {
        return new MovieDetails($this->title, $this->description);
    }
}
