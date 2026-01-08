<?php

namespace App\Catalog\Application\Model;

readonly class MovieListDto
{
    public function __construct(
        public int $total,
        /** @var MovieDto[] */
        public array $items,
    ) {
    }
}
