<?php

namespace App\Facilities\Domain\Ports;

use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Values\HallId;

interface HallRepository
{
    public function find(HallId $id): ?Hall;

    public function save(Hall $hall): void;
}
