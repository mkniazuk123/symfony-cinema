<?php

namespace App\Facilities\Application\Model;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatGroup;
use App\Facilities\Domain\Values\SeatNumber;

readonly class SeatGroupDto implements RowSegmentDto
{
    public static function fromDomain(SeatGroup $seatGroup): SeatGroupDto
    {
        return new SeatGroupDto($seatGroup->seats);
    }

    /**
     * @param list<SeatNumber> $seats
     */
    public function __construct(
        public array $seats,
    ) {
    }

    /**
     * @throws InvalidValueException
     */
    public function toDomain(): SeatGroup
    {
        return new SeatGroup($this->seats);
    }
}
