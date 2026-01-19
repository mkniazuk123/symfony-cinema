<?php

namespace App\Facilities\Application\Query;

use App\Core\Application\Query;
use App\Facilities\Domain\Values\HallId;

readonly class GetHallQuery implements Query
{
    public function __construct(public HallId $id)
    {
    }
}
