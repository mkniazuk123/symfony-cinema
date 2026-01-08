<?php

namespace App\Tests\Facilities\Fixtures;

use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Values\HallName;
use App\Facilities\Domain\Values\SeatingLayout;

class NewHallBuilder
{
    private HallName $name;
    private SeatingLayout $layout;

    public function __construct()
    {
        $this->name = new HallName('Default Hall Name');
        $this->layout = new SeatingLayoutBuilder()->addSampleRow()->build();
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
        return Hall::create($this->name, $this->layout);
    }
}
