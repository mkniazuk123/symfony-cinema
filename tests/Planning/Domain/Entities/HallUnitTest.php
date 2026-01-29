<?php

namespace App\Tests\Planning\Domain\Entities;

use App\Core\Domain\DateTime;
use App\Core\Domain\Duration;
use App\Planning\Domain\Events\ScreeningCreated;
use App\Planning\Domain\Exceptions\HallClosedException;
use App\Planning\Domain\Values\ScreeningId;
use App\Tests\Core\Fixtures\DateTimeBuilder;
use App\Tests\Helpers\DateTimeAssertions;
use App\Tests\Helpers\DomainEventsHelper;
use App\Tests\Planning\Fixtures\HallBuilder;
use App\Tests\Planning\Fixtures\MovieBuilder;
use PHPUnit\Framework\TestCase;

class HallUnitTest extends TestCase
{
    use DateTimeAssertions;

    public function testCreateScreening(): void
    {
        // Arrange:
        $hall = HallBuilder::create()->build();
        $movie = MovieBuilder::create()
            ->withDuration(Duration::minutes(120))
            ->build();
        $id = ScreeningId::generate();
        $startTime = DateTime::parse('2024-01-01T20:00:00Z');

        // Act:
        $screening = $hall->createScreening($id, $movie, $startTime);

        // Assert:
        $screeningCreated = DomainEventsHelper::forAggregateRoot($screening)
            ->getDomainEvent(ScreeningCreated::class);

        $this->assertEquals($id, $screeningCreated->id);
        $this->assertEquals($hall->getId(), $screeningCreated->hallId);
        $this->assertEquals($movie->getId(), $screeningCreated->movieId);
        $this->assertDateTimeEquals($startTime, $screeningCreated->time->start);
        $this->assertDateTimeEquals('2024-01-01T22:00:00Z', $screeningCreated->time->end);
    }

    public function testCannotCreateScreeningInClosedHall(): void
    {
        // Arrange:
        $hall = HallBuilder::create()
            ->closed()
            ->build();
        $movie = MovieBuilder::create()->build();
        $id = ScreeningId::generate();
        $startTime = DateTimeBuilder::future()->build();

        // Assert:
        $this->expectException(HallClosedException::class);

        // Act:
        $hall->createScreening($id, $movie, $startTime);
    }
}
