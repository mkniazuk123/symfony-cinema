<?php

namespace App\Facilities\Application\Query;

use App\Core\Application\Query;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Values\HallId;

/**
 * @implements Query<SeatingLayoutDto>
 */
readonly class GetHallLayoutQuery implements Query
{
    public function __construct(
        public HallId $id,
    ) {
    }
}
