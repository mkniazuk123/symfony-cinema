<?php

namespace App\Catalog\Interfaces\Requests;

use App\Catalog\Domain\Values\MovieLength;
use Symfony\Component\Validator\Constraints as Assert;

class MovieLengthRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    public int $minutes;

    public function build(): MovieLength
    {
        return new MovieLength($this->minutes);
    }
}
