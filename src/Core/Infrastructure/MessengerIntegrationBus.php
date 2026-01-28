<?php

namespace App\Core\Infrastructure;

use App\Core\Application\IntegrationBus;
use App\Core\Application\IntegrationEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerIntegrationBus implements IntegrationBus
{
    public function __construct(
        private MessageBusInterface $integrationBus,
    ) {
    }

    public function dispatch(IntegrationEvent $event): void
    {
        $this->integrationBus->dispatch($event);
    }
}
