<?php

namespace App\Facilities\Application\Model;

readonly class HallListDto
{
    public function __construct(
        public int $total,
        /** @var HallDto[] */
        public array $items,
    ) {
    }
}
