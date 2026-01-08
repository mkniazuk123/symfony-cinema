<?php

namespace App\Tests\Facilities\Fixtures;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\RowLayout;
use App\Facilities\Domain\Values\RowNumber;
use App\Facilities\Domain\Values\RowSegment;

class RowLayoutBuilder
{
    private RowNumber $number;
    /** @var list<RowSegment> */
    private array $segments;

    public function __construct(RowNumber|int|null $number = null)
    {
        if (is_int($number)) {
            $number = new RowNumber($number);
        }

        $this->number = $number ?? new RowNumber(1);
        $this->segments = [];
    }

    public function addSegment(RowSegment $segment): self
    {
        $this->segments[] = $segment;

        return $this;
    }

    /**
     * @throws InvalidValueException
     */
    public function build(): RowLayout
    {
        return new RowLayout($this->number, $this->segments);
    }
}
