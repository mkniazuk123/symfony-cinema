<?php

namespace App\Facilities\Domain\Values;

enum HallStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
