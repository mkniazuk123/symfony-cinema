<?php

namespace App\Facilities\Interfaces\Requests;

use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use Symfony\Component\Validator\Constraints as Assert;

class DateTimeRangeRequest
{
    #[Assert\NotBlank]
    public \DateTimeImmutable $start;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(propertyPath: 'start')]
    public \DateTimeImmutable $end;

    public function resolve(): DateTimeRange
    {
        return new DateTimeRange(new DateTime($this->start), new DateTime($this->end));
    }
}
