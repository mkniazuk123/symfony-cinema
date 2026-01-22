<?php

namespace App\Facilities\Application\Query;

use App\Core\Application\Query;
use App\Facilities\Application\Model\HallDto;
use App\Facilities\Domain\Values\HallId;

/**
 * @implements Query<HallDto>
 */
readonly class GetHallQuery implements Query
{
    public function __construct(
        public HallId $id,
    ) {
    }
}
