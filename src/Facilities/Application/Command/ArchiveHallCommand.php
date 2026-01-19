<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Facilities\Domain\Values\HallId;

readonly class ArchiveHallCommand implements Command
{
    public function __construct(
        public HallId $id,
    ) {
    }
}
