<?php

namespace App\Tests\Facilities\Fixtures;

use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Values\HallCapacity;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\HallStatus;
use App\Facilities\Domain\Values\SeatingLayout;

class HallBuilder
{
    private HallId $id;
    private HallStatus $status;
    private HallName $name;
    private HallCapacity $capacity;
    private SeatingLayout $layout;

    public function __construct(?HallId $id = null)
    {
        $this->id = $id ?? HallId::generate();
        $this->status = HallStatus::ACTIVE;
        $this->name = new HallName('Default Hall Name');
        $this->capacity = new HallCapacity(100);
        $this->layout = new SeatingLayoutBuilder()->addSampleRow()->build();
    }

    public function withId(HallId|string $id): self
    {
        if (is_string($id)) {
            $id = new HallId($id);
        }
        $this->id = $id;

        return $this;
    }

    public function withStatus(HallStatus|string $status): self
    {
        if (is_string($status)) {
            $status = HallStatus::from($status);
        }
        $this->status = $status;

        return $this;
    }

    public function withName(HallName|string $name): self
    {
        if (is_string($name)) {
            $name = new HallName($name);
        }
        $this->name = $name;

        return $this;
    }

    public function withCapacity(HallCapacity|int $capacity): self
    {
        if (is_int($capacity)) {
            $capacity = new HallCapacity($capacity);
        }
        $this->capacity = $capacity;

        return $this;
    }

    public function withLayout(SeatingLayout $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function build(): Hall
    {
        return Hall::reconstitute($this->id, $this->status, $this->name, $this->capacity, $this->layout);
    }
}
