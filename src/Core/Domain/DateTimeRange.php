<?php

namespace App\Core\Domain;

/**
 * Represents a [start, end) date time range.
 */
final readonly class DateTimeRange extends Value
{
    public static function parse(
        string $start,
        string $end,
    ): self {
        return new self(
            DateTime::parse($start),
            DateTime::parse($end),
        );
    }

    public static function startingAt(
        DateTime $start,
        Duration $duration,
    ): self {
        return new self(
            $start,
            $start->add($duration),
        );
    }

    /**
     * @throws InvalidValueException
     */
    final public function __construct(
        public DateTime $start,
        public DateTime $end,
    ) {
        if ($end->isBefore($start)) {
            throw new InvalidValueException('Date time range must not have end before start.');
        }
    }

    public function equals(Value $other): bool
    {
        return $other instanceof self && $other->start->equals($this->start) && $other->end->equals($this->end);
    }

    public function isFuture(Clock $clock): bool
    {
        return $this->start->isFuture($clock);
    }

    public function overlaps(self $other): bool
    {
        return !$this->doesNotOverlap($other);
    }

    public function fitsIn(self $other): bool
    {
        return
            ($this->start->equals($other->start) || $this->start->isAfter($other->start))
            && !$this->end->isAfter($other->end)
        ;
    }

    public function expandedBy(Duration $duration): self
    {
        return new self(
            $this->start->subtract($duration),
            $this->end->add($duration),
        );
    }

    /**
     * @see https://stackoverflow.com/a/325964
     */
    private function doesNotOverlap(self $other): bool
    {
        return !$other->end->isAfter($this->start) || !$this->end->isAfter($other->start);
    }
}
