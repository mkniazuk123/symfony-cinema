<?php

namespace App\Tests\Planning\Fixtures;

use App\Planning\Domain\Entities\Hall;
use App\Planning\Domain\Values\HallId;

class HallBuilder
{
    public static function create(HallId|string|null $id = null): self
    {
        if (is_string($id)) {
            $id = new HallId($id);
        }

        return new self($id);
    }

    private HallId $id;
    private bool $open;

    private function __construct(?HallId $id = null)
    {
        $this->id = $id ?? HallId::generate();
        $this->open = true;
    }

    public function closed(): self
    {
        $this->open = false;

        return $this;
    }

    public function build(): Hall
    {
        return Hall::reconstitute(
            $this->id,
            $this->open,
        );
    }
}
