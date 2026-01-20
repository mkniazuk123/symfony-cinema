<?php

namespace App\Core\Domain;

/**
 * @template T of object
 *
 * @extends Entity<T>
 */
abstract class AggregateRoot extends Entity
{
    /**
     * @var list<DomainEvent>
     */
    private array $domainEvents = [];

    /**
     * @return list<DomainEvent>
     */
    final public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }

    final public function publishDomainEvents(DomainEventBus $bus): void
    {
        foreach ($this->pullDomainEvents() as $domainEvent) {
            $bus->dispatch($domainEvent);
        }
    }

    final protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }
}
