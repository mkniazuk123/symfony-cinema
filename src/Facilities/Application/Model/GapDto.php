<?php

namespace App\Facilities\Application\Model;

use App\Facilities\Domain\Values\Gap;

readonly class GapDto implements RowSegmentDto
{
    public static function fromDomain(Gap $gap): GapDto
    {
        return new GapDto();
    }

    public function toDomain(): Gap
    {
        return new Gap();
    }
}
