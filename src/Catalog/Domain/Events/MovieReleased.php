<?php

namespace App\Catalog\Domain\Events;

use App\Catalog\Domain\Values\MovieId;
use App\Core\Domain\DomainEvent;

readonly class MovieReleased implements DomainEvent
{
    public function __construct(
        public MovieId $id,
    ) {
    }
}
