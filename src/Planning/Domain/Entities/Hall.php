<?php

namespace App\Planning\Domain\Entities;

use App\Core\Domain\DateTime;
use App\Planning\Domain\Exceptions\HallClosedException;
use App\Planning\Domain\Exceptions\MovieUnavailableException;
use App\Planning\Domain\Values\HallId;
use App\Planning\Domain\Values\ScreeningId;

class Hall
{
    public static function create(HallId $id, bool $open): self
    {
        return new self($id, $open);
    }

    public static function reconstitute(HallId $id, bool $open): self
    {
        return new self($id, $open);
    }

    private function __construct(
        private HallId $id,
        private bool $open,
    ) {
    }

    public function getId(): HallId
    {
        return $this->id;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function open(): void
    {
        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
    }

    /**
     * @throws MovieUnavailableException
     * @throws HallClosedException
     */
    public function createScreening(ScreeningId $id, Movie $movie, DateTime $startTime): Screening
    {
        if (!$this->isOpen()) {
            throw new HallClosedException($this->id);
        }

        $time = $movie->schedule($startTime);

        return Screening::create($id, $this->id, $movie->getId(), $time);
    }
}
