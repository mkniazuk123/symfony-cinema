<?php

namespace App\Catalog\API\Events;

use App\Catalog\API\Model\MovieDetails;
use App\Catalog\API\Model\MovieLength;
use App\Core\Application\IntegrationEvent;

readonly class MovieCreated extends IntegrationEvent
{
    public function __construct(
        public string $id,
        public string $status,
        public MovieDetails $details,
        public MovieLength $length,
    ) {
    }
}
