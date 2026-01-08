<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\SeatingLayoutDto;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateHallLayoutRequest
{
    #[Assert\NotNull]
    #[Assert\Valid]
    public SeatingLayoutRequest $layout;

    /**
     * @return array{layout: SeatingLayoutDto}
     */
    public function resolve(): array
    {
        return [
            'layout' => $this->layout->resolve(),
        ];
    }
}
