<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\RowSegmentDto;
use Symfony\Component\Serializer\Attribute as Serializer;

#[Serializer\DiscriminatorMap(
    typeProperty: 'type',
    mapping: [
        'seatGroup' => SeatGroupRequest::class,
        'gap' => GapRequest::class,
    ],
)]
interface RowSegmentRequest
{
    public function resolve(): RowSegmentDto;
}
