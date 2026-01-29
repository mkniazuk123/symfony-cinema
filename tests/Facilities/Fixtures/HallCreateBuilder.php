<?php

namespace App\Tests\Facilities\Fixtures;

use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\SeatingLayout;

class HallCreateBuilder
{
    public static function create(): self
    {
        return new self();
    }

    private HallId $id;
    private HallName $name;
    private SeatingLayout $layout;

    private function __construct()
    {
        $this->id = HallId::generate();
        $this->name = new HallName('Default Hall Name');
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

    public function withName(HallName|string $name): self
    {
        if (is_string($name)) {
            $name = new HallName($name);
        }
        $this->name = $name;

        return $this;
    }

    public function withLayout(SeatingLayout $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function build(): Hall
    {
        return Hall::create($this->id, $this->name, $this->layout);
    }
}
