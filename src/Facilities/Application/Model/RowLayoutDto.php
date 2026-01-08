<?php

namespace App\Facilities\Application\Model;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\Gap;
use App\Facilities\Domain\Values\RowLayout;
use App\Facilities\Domain\Values\RowNumber;
use App\Facilities\Domain\Values\RowSegment;
use App\Facilities\Domain\Values\SeatGroup;

readonly class RowLayoutDto
{
    public static function fromDomain(RowLayout $rowLayout): RowLayoutDto
    {
        return new self(
            $rowLayout->number,
            array_map(fn (RowSegment $segment) => match (get_class($segment)) {
                SeatGroup::class => SeatGroupDto::fromDomain($segment),
                Gap::class => GapDto::fromDomain($segment),
                default => throw new \RuntimeException('Unknown segment type: '.get_class($segment)),
            }, $rowLayout->segments)
        );
    }

    /**
     * @param list<RowSegmentDto> $segments
     */
    public function __construct(
        public RowNumber $number,
        public array $segments,
    ) {
    }

    /**
     * @throws InvalidValueException
     */
    public function toDomain(): RowLayout
    {
        return new RowLayout(
            $this->number,
            array_map(fn (RowSegmentDto $segment) => $segment->toDomain(), $this->segments)
        );
    }
}
