<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\RowLayoutDto;
use App\Facilities\Domain\Values\RowNumber;
use Symfony\Component\Validator\Constraints as Assert;

class RowLayoutRequest
{
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    #[Assert\Type('integer')]
    public int $number;

    /** @var list<RowSegmentRequest> */
    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\Valid]
    #[Assert\Count(min: 1)]
    #[Assert\All([
        new Assert\Type(RowSegmentRequest::class),
    ])]
    public array $segments;

    public function resolve(): RowLayoutDto
    {
        return new RowLayoutDto(
            number: new RowNumber($this->number),
            segments: array_map(fn (RowSegmentRequest $segment) => $segment->resolve(), $this->segments),
        );
    }
}
