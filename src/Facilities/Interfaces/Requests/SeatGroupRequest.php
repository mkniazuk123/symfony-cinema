<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\SeatGroupDto;
use App\Facilities\Domain\Values\SeatNumber;
use Symfony\Component\Validator\Constraints as Assert;

class SeatGroupRequest implements RowSegmentRequest
{
    /** @var list<int> */
    #[Assert\NotNull]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\GreaterThan(0),
    ])]
    #[Assert\Count(min: 1)]
    public array $seats;

    public function resolve(): SeatGroupDto
    {
        return new SeatGroupDto(array_map(fn (int $number) => new SeatNumber($number), $this->seats));
    }
}
