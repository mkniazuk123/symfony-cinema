<?php

namespace App\Catalog\API\Events;

use App\Catalog\API\Model\MovieLength;
use App\Core\Application\IntegrationEvent;

readonly class MovieLengthUpdated extends IntegrationEvent
{
    public function __construct(
        public string $id,
        public MovieLength $length,
    ) {
    }
}
