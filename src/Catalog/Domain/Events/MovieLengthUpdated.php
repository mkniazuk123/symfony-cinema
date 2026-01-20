<?php

namespace App\Catalog\Domain\Events;

use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Core\Domain\DomainEvent;

readonly class MovieLengthUpdated implements DomainEvent
{
    public function __construct(
        public MovieId $id,
        public MovieLength $length,
    ) {
    }
}
