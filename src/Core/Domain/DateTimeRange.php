<?php

namespace App\Core\Domain;

/**
 * Represents a [start, end) date time range.
 */
readonly class DateTimeRange
{
    public static function parse(
        string $start,
        string $end,
    ): static {
        return new static(
            DateTime::parse($start),
            DateTime::parse($end),
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

    public function equals(self $other): bool
    {
        return $other->start->equals($this->start) && $other->end->equals($this->end);
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

    /**
     * @see https://stackoverflow.com/a/325964
     */
    private function doesNotOverlap(self $other): bool
    {
        return !$other->end->isAfter($this->start) || !$this->end->isAfter($other->start);
    }
}
