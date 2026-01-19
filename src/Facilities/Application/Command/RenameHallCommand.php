<?php

namespace App\Facilities\Application\Command;

use App\Core\Application\Command;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;

readonly class RenameHallCommand implements Command
{
    public function __construct(
        public HallId $id,
        public HallName $name,
    ) {
    }
}
