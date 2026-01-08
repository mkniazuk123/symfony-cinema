<?php

namespace App\Facilities\Application\Model;

use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;

readonly class HallDto
{
    public function __construct(
        public HallId $id,
        public HallStatus $status,
        public HallName $name,
        public HallCapacity $capacity,
    ) {
    }
}
