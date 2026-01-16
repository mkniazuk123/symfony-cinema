<?php

namespace App\Facilities\Domain\Values;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Entities\Reservation;

/**
 * @implements \IteratorAggregate<int, Reservation>
 */
class Reservations implements \IteratorAggregate
{
    /** @var array<string, Reservation> */
    private array $items = [];

    /**
     * @param iterable<Reservation> $items
     *
     * @throws InvalidValueException
     */
    public function __construct(iterable $items = [])
    {
        $this->fill($items);
    }

    /**
     * @return list<Reservation>
     */
    public function getItems(): array
    {
        return array_values($this->items);
    }

    public function add(Reservation $reservation): void
    {
        $key = (string) $reservation->getId();

        if (isset($this->items[$key])) {
            throw new InvalidValueException(sprintf('Duplicate reservation "%s"', $key));
        } else {
            $this->items[$key] = $reservation;
        }
    }

    /**
     * @param \Closure(Reservation): bool $predicate
     */
    public function filter(\Closure $predicate): self
    {
        return new self(array_filter($this->items, $predicate));
    }

    /**
     * @param \Closure(Reservation): void $callback
     */
    public function each(\Closure $callback): void
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getItems());
    }

    /**
     * @throws InvalidValueException
     */
    private function fill(iterable $items): void
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }
}
