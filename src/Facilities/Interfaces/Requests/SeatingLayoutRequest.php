<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\SeatingLayoutDto;
use Symfony\Component\Validator\Constraints as Assert;

class SeatingLayoutRequest
{
    /** @var list<RowLayoutRequest> */
    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    #[Assert\All([
        new Assert\Type(RowLayoutRequest::class),
    ])]
    public array $rows;

    public function resolve(): SeatingLayoutDto
    {
        return new SeatingLayoutDto(
            rows: array_map(fn (RowLayoutRequest $row) => $row->resolve(), $this->rows),
        );
    }
}
