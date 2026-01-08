<?php

namespace App\Core\Domain;

trait DomainEventsTrait
{
    /** @var DomainEvent[] */
    private array $domainEvents = [];

    private function recordDomainEvent(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * @return DomainEvent[]
     */
    public function releaseDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    public function publishDomainEvents(DomainEventBus $bus): void
    {
        foreach ($this->releaseDomainEvents() as $domainEvent) {
            $bus->dispatch($domainEvent);
        }
    }
}
