<?php

namespace App\Facilities\Interfaces\Requests;

use App\Facilities\Application\Model\GapDto;

class GapRequest implements RowSegmentRequest
{
    public function resolve(): GapDto
    {
        return new GapDto();
    }
}
