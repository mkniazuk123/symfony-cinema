<?php

namespace App\Facilities\Domain\Values;

enum ReservationStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
