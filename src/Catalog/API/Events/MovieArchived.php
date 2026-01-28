<?php

namespace App\Catalog\API\Events;

use App\Core\Application\IntegrationEvent;

readonly class MovieArchived extends IntegrationEvent
{
    public function __construct(
        public string $id,
    ) {
    }
}
