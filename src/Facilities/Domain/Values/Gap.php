<?php

namespace App\Facilities\Domain\Values;

readonly class Gap implements RowSegment
{
    public function countSeats(): int
    {
        return 0;
    }
}
