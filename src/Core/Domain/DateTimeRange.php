<?php

namespace App\Core\Domain;

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
}
