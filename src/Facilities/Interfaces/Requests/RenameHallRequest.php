<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Domain\Values\HallName;
use Symfony\Component\Validator\Constraints as Assert;

class RenameHallRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $name;

    /**
     * @return array{name: HallName}
     */
    public function resolve(): array
    {
        return [
            'name' => new HallName($this->name),
        ];
    }
}
