<?php

namespace App\Catalog\Application\Command;

use App\Catalog\Domain\Values\MovieId;
use App\Core\Application\Command;

readonly class ReleaseMovie implements Command
{
    public function __construct(
        public MovieId $id,
    ) {
    }
}
