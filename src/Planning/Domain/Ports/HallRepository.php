<?php

namespace App\Planning\Domain\Ports;

use App\Planning\Domain\Entities\Hall;
use App\Planning\Domain\Values\HallId;

interface HallRepository
{
    public function find(HallId $id): ?Hall;

    public function save(Hall $hall): void;
}
