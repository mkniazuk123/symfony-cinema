<?php

namespace App\Catalog\Domain\Events;

use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Core\Domain\DomainEvent;

readonly class MovieDetailsUpdated implements DomainEvent
{
    public function __construct(
        public MovieId $id,
        public MovieDetails $details,
    ) {
    }
}
