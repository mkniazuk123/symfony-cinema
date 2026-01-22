<?php

namespace App\Planning\Domain\Entities;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Core\Domain\Duration;
use App\Planning\Domain\Exceptions\MovieUnavailableException;
use App\Planning\Domain\Values\MovieId;

class Movie
{
    public static function create(MovieId $id, Duration $duration, bool $available): self
    {
        return new self($id, $duration, $available);
    }

    public static function reconstitute(MovieId $id, Duration $duration, bool $available): self
    {
        return new self($id, $duration, $available);
    }

    private function __construct(
        private MovieId $id,
        private Duration $duration,
        private bool $available,
    ) {
    }

    public function getId(): MovieId
    {
        return $this->id;
    }

    public function getDuration(): Duration
    {
        return $this->duration;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function available(): void
    {
        $this->available = true;
    }

    public function unavailable(): void
    {
        $this->available = false;
    }

    /**
     * @throws MovieUnavailableException
     */
    public function schedule(DateTime $startTime): DateTimeRange
    {
        if (!$this->isAvailable()) {
            throw new MovieUnavailableException($this->id);
        }

        return DateTimeRange::startingAt($startTime, $this->duration);
    }
}
