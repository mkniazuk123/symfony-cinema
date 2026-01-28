<?php

namespace App\Catalog\API\REST;

use App\Catalog\Domain\Values\MovieDescription;
use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieTitle;
use Symfony\Component\Validator\Constraints as Assert;

class MovieDetailsRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $description;

    public function build(): MovieDetails
    {
        return new MovieDetails(
            title: new MovieTitle($this->title),
            description: new MovieDescription($this->description),
        );
    }
}
