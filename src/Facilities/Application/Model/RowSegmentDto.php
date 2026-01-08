<?php

namespace App\Facilities\Application\Model;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\RowSegment;
use Symfony\Component\Serializer\Attribute as Serializer;

#[Serializer\DiscriminatorMap(
    typeProperty: 'type',
    mapping: [
        'seatGroup' => SeatGroupDto::class,
        'gap' => GapDto::class,
    ],
)]
interface RowSegmentDto
{
    /**
     * @throws InvalidValueException
     */
    public function toDomain(): RowSegment;
}
