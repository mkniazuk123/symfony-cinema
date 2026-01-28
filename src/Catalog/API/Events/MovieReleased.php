<?php

namespace App\Catalog\API\Events;

use App\Core\Application\IntegrationEvent;

readonly class MovieReleased extends IntegrationEvent
{
    public function __construct(
        public string $id,
    ) {
    }
}
