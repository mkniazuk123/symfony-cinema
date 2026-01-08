<?php

namespace App\Catalog\Domain\Values;

enum MovieStatus: string
{
    case UPCOMING = 'upcoming';
    case AVAILABLE = 'available';
    case ARCHIVED = 'archived';
}
