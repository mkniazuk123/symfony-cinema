<?php

namespace App\Facilities\Application\Ports;

use App\Facilities\Application\Model\HallDto;
use App\Facilities\Application\Model\HallListDto;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Values\HallId;

interface HallReadModel
{
    public function readHall(HallId $id): ?HallDto;

    public function readHallLayout(HallId $id): ?SeatingLayoutDto;

    public function readHalls(): HallListDto;
}
