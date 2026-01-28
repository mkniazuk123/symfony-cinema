<?php

namespace App\Planning\Domain\Services;

use App\Core\Domain\Clock;
use App\Core\Domain\DateTime;
use App\Core\Domain\DateTimeRange;
use App\Planning\Domain\Entities\Screening;
use App\Planning\Domain\Exceptions\InsufficientTimeException;
use App\Planning\Domain\Exceptions\TimeConflictException;
use App\Planning\Domain\Policies\SchedulingPolicy;
use App\Planning\Domain\Ports\ScreeningRepository;
use App\Planning\Domain\Values\HallId;

class SchedulerService
{
    public function __construct(
        private SchedulingPolicy $policy,
        private ScreeningRepository $repository,
        private Clock $clock,
    ) {
    }

    /**
     * @throws InsufficientTimeException
     * @throws TimeConflictException
     */
    public function scheduleScreening(Screening $screening): void
    {
        $time = $screening->getTime();
        $this->checkAdvance($time->start);
        $this->checkConflicts($screening->getHallId(), $time);
        $this->repository->save($screening);
    }

    /**
     * @throws InsufficientTimeException
     */
    private function checkAdvance(DateTime $time): void
    {
        $advance = $this->policy->getMinimumAdvancePeriod();
        $schedulingBoundary = DateTime::current($this->clock)->add($advance);
        if ($time->isBefore($schedulingBoundary)) {
            throw new InsufficientTimeException($time, sprintf('Screenings must be scheduled at least %s seconds in advance.', $advance->inSeconds()));
        }
    }

    /**
     * @throws TimeConflictException
     */
    private function checkConflicts(HallId $hallId, DateTimeRange $time): void
    {
        $expandedTime = $time->expandedBy($this->policy->getMinimumScreeningGap());
        if ($this->repository->hasConflict($hallId, $expandedTime)) {
            throw new TimeConflictException($hallId, $time);
        }
    }
}
