<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Facilities\Domain\Values\HallId;

readonly class CloseHallCommand implements Command
{
    public function __construct(
        public HallId $id,
    ) {
    }
}
