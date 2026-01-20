<?php

namespace App\Catalog\Domain\Events;

use App\Catalog\Domain\Values\MovieDetails;
use App\Catalog\Domain\Values\MovieId;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Core\Domain\DomainEvent;

readonly class MovieCreated implements DomainEvent
{
    public function __construct(
        public MovieId $id,
        public MovieStatus $status,
        public MovieDetails $details,
        public MovieLength $length,
    ) {
    }
}
