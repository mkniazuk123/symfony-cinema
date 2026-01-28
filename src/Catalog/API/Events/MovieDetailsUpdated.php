<?php

namespace App\Catalog\API\Events;

use App\Catalog\API\Model\MovieDetails;
use App\Core\Application\IntegrationEvent;

readonly class MovieDetailsUpdated extends IntegrationEvent
{
    public function __construct(
        public string $id,
        public MovieDetails $details,
    ) {
    }
}
