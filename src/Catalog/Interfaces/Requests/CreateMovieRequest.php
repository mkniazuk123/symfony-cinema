<?php

namespace App\Catalog\Interfaces\Requests;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMovieRequest
{
    #[Assert\NotNull]
    #[Assert\Valid]
    public MovieDetailsRequest $details;

    #[Assert\NotNull]
    #[Assert\Valid]
    public MovieLengthRequest $length;
}
