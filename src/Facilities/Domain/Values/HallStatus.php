<?php

namespace App\Facilities\Domain\Values;

enum HallStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';
}
