<?php

namespace App\Tests\Helpers;

use App\Core\Domain\AggregateRoot;
use App\Core\Domain\DomainEvent;
use PHPUnit\Framework\Assert;

class DomainEventsHelper
{
    /**
     * @template T of object
     *
     * @param AggregateRoot<T> $aggregateRoot
     */
    public static function forAggregateRoot(AggregateRoot $aggregateRoot): self
    {
        return new self($aggregateRoot->pullDomainEvents());
    }

    /**
     * @param array<DomainEvent> $events
     */
    public function __construct(
        private array $events = [],
    ) {
    }

    /**
     * @param class-string<DomainEvent> $class
     */
    public function assertDomainEventIsDispatched(string $class, int $count = 1): void
    {
        $events = $this->getDomainEvents($class);

        if (count($events) !== $count) {
            Assert::fail(sprintf(
                'Expected to dispatch %d events of type %s, but dispatched %d.',
                $count,
                $class,
                count($events),
            ));
        }
    }

    /**
     * @param class-string<DomainEvent> $class
     */
    public function assertDomainEventIsNotDispatched(string $class): void
    {
        $events = $this->getDomainEvents($class);

        if (count($events) > 0) {
            Assert::fail(sprintf(
                'Expected not to dispatch any events of type %s, but dispatched %d.',
                $class,
                count($events),
            ));
        }
    }

    /**
     * @template T of DomainEvent
     *
     * @param class-string<T> $class
     *
     * @return T[]
     */
    public function getDomainEvents(string $class): array
    {
        $events = array_values(array_filter($this->events, fn (DomainEvent $event) => $event instanceof $class));
        Assert::assertContainsOnlyInstancesOf($class, $events);

        return $events;
    }

    /**
     * @template T of DomainEvent
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function getDomainEvent(string $class): DomainEvent
    {
        $events = $this->getDomainEvents($class);

        if (1 !== count($events)) {
            Assert::fail(sprintf(
                'Expected to get 1 event of type %s, but got %d.',
                $class,
                count($events),
            ));
        }

        $event = $events[0];
        Assert::assertInstanceOf($class, $event);

        return $event;
    }
}
