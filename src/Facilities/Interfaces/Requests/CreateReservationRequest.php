<?php

namespace App\Facilities\Interfaces\Requests;

use App\Core\Domain\DateTimeRange;
use Symfony\Component\Validator\Constraints as Assert;

class CreateReservationRequest
{
    #[Assert\NotNull]
    #[Assert\Valid]
    public DateTimeRangeRequest $time;

    /**
     * @return array{time: DateTimeRange}
     */
    public function resolve(): array
    {
        return [
            'time' => $this->time->resolve(),
        ];
    }
}
