<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;

readonly class RowLayout
{
    /**
     * @param list<RowSegment> $segments
     *
     * @throws InvalidValueException
     */
    public function __construct(
        public RowNumber $number,
        public array $segments,
    ) {
        $this->assertList();
        $this->assertUniqueSegments();
        $this->assertHasSeats();
        $this->assertUniqueSeats();
    }

    public function countSeats(): int
    {
        return array_sum(
            array_map(
                fn (RowSegment $segment) => $segment->countSeats(),
                $this->segments,
            ),
        );
    }

    /**
     * @throws InvalidValueException
     */
    private function assertList(): void
    {
        if (!array_is_list($this->segments)) {
            throw new InvalidValueException('Row layout segments must be a list');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertUniqueSegments(): void
    {
        $lastSegmentClass = null;
        foreach ($this->segments as $segment) {
            $currentSegmentClass = get_class($segment);
            if ($currentSegmentClass === $lastSegmentClass) {
                throw new InvalidValueException("Cannot define segment of type $currentSegmentClass twice in a row");
            }

            $lastSegmentClass = $currentSegmentClass;
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertHasSeats(): void
    {
        if (!array_any($this->segments, fn ($segment) => $segment instanceof SeatGroup)) {
            throw new InvalidValueException('Row layout must contain at least one seat group');
        }
    }

    /**
     * @throws InvalidValueException
     */
    private function assertUniqueSeats(): void
    {
        $seatNumbers = [];
        foreach ($this->segments as $segment) {
            if ($segment instanceof SeatGroup) {
                foreach ($segment->seats as $seat) {
                    $number = $seat->value();
                    if (in_array($number, $seatNumbers, true)) {
                        throw new InvalidValueException("Duplicate seat number $number in row {$this->number->value()}");
                    }

                    $seatNumbers[] = $number;
                }
            }
        }
    }
}
