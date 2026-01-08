<?php

namespace App\Facilities\Application\Model;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Domain\Values\SeatingLayout;

readonly class SeatingLayoutDto
{
    public static function fromDomain(SeatingLayout $layout): self
    {
        return new self(
            array_map(fn ($row) => RowLayoutDto::fromDomain($row), $layout->rows)
        );
    }

    /**
     * @param list<RowLayoutDto> $rows
     */
    public function __construct(
        public array $rows,
    ) {
    }

    /**
     * @throws InvalidValueException
     */
    public function toDomain(): SeatingLayout
    {
        return new SeatingLayout(
            array_map(fn (RowLayoutDto $row) => $row->toDomain(), $this->rows)
        );
    }
}
