<?php

namespace App\Facilities\Domain\Entities;

use App\Core\Domain\AggregateRoot;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Facilities\Domain\Values\SeatingLayout;

/**
 * @extends AggregateRoot<HallId>
 */
class Hall extends AggregateRoot
{
    public static function create(HallId $id, HallName $name, SeatingLayout $layout): self
    {
        $status = HallStatus::OPEN;
        $capacity = new HallCapacity($layout->countSeats());

        return new self($id, $status, $name, $capacity, $layout);
    }

    public static function reconstitute(
        HallId $id,
        HallStatus $status,
        HallName $name,
        HallCapacity $capacity,
        SeatingLayout $layout,
    ): self {
        return new self($id, $status, $name, $capacity, $layout);
    }

    private function __construct(
        HallId $id,
        private HallStatus $status,
        private HallName $name,
        private HallCapacity $capacity,
        private SeatingLayout $layout,
    ) {
        parent::__construct($id);
    }

    public function getStatus(): HallStatus
    {
        return $this->status;
    }

    public function isOpen(): bool
    {
        return HallStatus::OPEN === $this->status;
    }

    public function isClosed(): bool
    {
        return HallStatus::CLOSED === $this->status;
    }

    public function getName(): HallName
    {
        return $this->name;
    }

    public function getCapacity(): HallCapacity
    {
        return $this->capacity;
    }

    public function getLayout(): SeatingLayout
    {
        return $this->layout;
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function rename(HallName $name): void
    {
        $this->assertStatus(HallStatus::OPEN, HallStatus::CLOSED);
        $this->name = $name;
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function updateLayout(SeatingLayout $layout): void
    {
        $this->assertStatus(HallStatus::OPEN, HallStatus::CLOSED);

        $this->layout = $layout;
        $this->capacity = new HallCapacity($layout->countSeats());
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function close(): void
    {
        $this->assertStatus(HallStatus::OPEN);

        $this->status = HallStatus::CLOSED;
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function open(): void
    {
        $this->assertStatus(HallStatus::CLOSED);

        $this->status = HallStatus::OPEN;
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function archive(): void
    {
        $this->assertStatus(HallStatus::OPEN, HallStatus::CLOSED);

        $this->status = HallStatus::ARCHIVED;
    }

    /**
     * @throws InvalidHallStatusException
     */
    private function assertStatus(HallStatus ...$statuses): void
    {
        if (!in_array($this->status, $statuses, true)) {
            throw new InvalidHallStatusException($this->id, $this->status);
        }
    }
}
