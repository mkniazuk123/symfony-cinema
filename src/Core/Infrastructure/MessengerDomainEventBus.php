<?php

namespace App\Core\Infrastructure;

use App\Core\Domain\DomainEvent;
use App\Core\Domain\DomainEventBus;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerDomainEventBus implements DomainEventBus
{
    public function __construct(
        private MessageBusInterface $domainEventBus,
    ) {
    }

    public function dispatch(DomainEvent $event): void
    {
        $this->domainEventBus->dispatch($event);
    }
}
