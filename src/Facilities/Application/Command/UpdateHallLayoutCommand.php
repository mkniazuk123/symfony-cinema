<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Domain\Values\HallId;

readonly class UpdateHallLayoutCommand implements Command
{
    public function __construct(
        public HallId $id,
        public SeatingLayoutDto $layout,
    ) {
    }
}
