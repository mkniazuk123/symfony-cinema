<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Values\HallName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateHallRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Valid]
    public SeatingLayoutRequest $layout;

    /**
     * @return array{name: HallName, layout: SeatingLayoutDto}
     */
    public function resolve(): array
    {
        return [
            'name' => new HallName($this->name),
            'layout' => $this->layout->resolve(),
        ];
    }
}
