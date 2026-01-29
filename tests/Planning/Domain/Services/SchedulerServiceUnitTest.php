<?php

namespace App\Tests\Planning\Domain\Services;

use App\Core\Domain\Clock;
use App\Core\Domain\DateTimeRange;
use App\Core\Domain\Duration;
use App\Planning\Domain\Exceptions\InsufficientTimeException;
use App\Planning\Domain\Exceptions\TimeConflictException;
use App\Planning\Domain\Policies\SchedulingPolicy;
use App\Planning\Domain\Ports\ScreeningRepository;
use App\Planning\Domain\Services\SchedulerService;
use App\Tests\Core\Fixtures\DateTimeBuilder;
use App\Tests\Planning\Fixtures\ScreeningBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class SchedulerServiceUnitTest extends TestCase
{
    private Stub $schedulingPolicyMock;
    private MockObject $screeningRepositoryMock;
    private Stub $clockMock;
    private SchedulerService $schedulerService;

    protected function setUp(): void
    {
        $this->schedulingPolicyMock = $this->createStub(SchedulingPolicy::class);
        $this->screeningRepositoryMock = $this->createMock(ScreeningRepository::class);
        $this->clockMock = $this->createStub(Clock::class);
        $this->schedulerService = new SchedulerService(
            $this->schedulingPolicyMock,
            $this->screeningRepositoryMock,
            $this->clockMock
        );
    }

    public function testCannotScheduleScreeningNotRespectingAdvancePeriod(): void
    {
        // Arrange:
        $now = DateTimeBuilder::past()->build();
        $this->clockMock
            ->method('now')
            ->willReturn($now->value());

        $this->schedulingPolicyMock
            ->method('getMinimumAdvancePeriod')
            ->willReturn(Duration::hours(24));

        $screening = ScreeningBuilder::create()
            ->withTime(
                new DateTimeRange(
                    $now->add(Duration::hours(24))->subtract(Duration::seconds(1)),
                    $now->add(Duration::hours(26))
                )
            )
            ->build();

        // Assert:
        $this->expectException(InsufficientTimeException::class);
        $this->expectExceptionMessageMatches('/in advance/');

        // Act:
        $this->schedulerService->scheduleScreening($screening);
    }

    public function testCannotScheduleScreeningWithConflict(): void
    {
        // Arrange:
        $now = DateTimeBuilder::past()->build();
        $this->clockMock
            ->method('now')
            ->willReturn($now->value());

        $this->schedulingPolicyMock
            ->method('getMinimumAdvancePeriod')
            ->willReturn(Duration::hours(1));

        $this->schedulingPolicyMock
            ->method('getMinimumScreeningGap')
            ->willReturn(Duration::minutes(15));

        $screening = ScreeningBuilder::create()
            ->withTime(
                new DateTimeRange(
                    $now->add(Duration::hours(1)),
                    $now->add(Duration::hours(2))
                )
            )
            ->build();

        $this->screeningRepositoryMock
            ->method('hasConflict')
            ->with(
                $screening->getHallId(),
                $screening->getTime()->expandedBy(Duration::minutes(15))
            )
            ->willReturn(true);

        // Assert:
        $this->expectException(TimeConflictException::class);

        // Act:
        $this->schedulerService->scheduleScreening($screening);
    }

    public function testScheduleScreening(): void
    {
        // Arrange:
        $now = DateTimeBuilder::past()->build();
        $this->clockMock
            ->method('now')
            ->willReturn($now->value());

        $this->schedulingPolicyMock
            ->method('getMinimumAdvancePeriod')
            ->willReturn(Duration::hours(1));

        $this->schedulingPolicyMock
            ->method('getMinimumScreeningGap')
            ->willReturn(Duration::minutes(15));

        $screening = ScreeningBuilder::create()
            ->withTime(
                new DateTimeRange(
                    $now->add(Duration::hours(2)),
                    $now->add(Duration::hours(3))
                )
            )
            ->build();

        $this->screeningRepositoryMock
            ->method('hasConflict')
            ->with(
                $screening->getHallId(),
                $screening->getTime()->expandedBy(Duration::minutes(15))
            )
            ->willReturn(false);

        $this->screeningRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($screening);

        // Act:
        $this->schedulerService->scheduleScreening($screening);
    }
}
