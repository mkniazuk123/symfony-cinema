<?php

namespace App\Facilities\Domain\Entities;

use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Facilities\Domain\Values\SeatingLayout;

class Hall
{
    public static function create(HallName $name, SeatingLayout $layout): self
    {
        $id = HallId::generate();
        $status = HallStatus::ACTIVE;
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
        private HallId $id,
        private HallStatus $status,
        private HallName $name,
        private HallCapacity $capacity,
        private SeatingLayout $layout,
    ) {
    }

    public function getId(): HallId
    {
        return $this->id;
    }

    public function getStatus(): HallStatus
    {
        return $this->status;
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
        $this->assertStatus(HallStatus::ACTIVE);
        $this->name = $name;
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function updateLayout(SeatingLayout $layout): void
    {
        $this->assertStatus(HallStatus::ACTIVE);

        $this->layout = $layout;
        $this->capacity = new HallCapacity($layout->countSeats());
    }

    /**
     * @throws InvalidHallStatusException
     */
    public function archive(): void
    {
        $this->assertStatus(HallStatus::ACTIVE);

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
