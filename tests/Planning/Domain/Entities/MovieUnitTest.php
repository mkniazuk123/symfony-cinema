<?php

namespace App\Tests\Planning\Domain\Entities;

use App\Core\Domain\DateTime;
use App\Core\Domain\Duration;
use App\Planning\Domain\Exceptions\MovieUnavailableException;
use App\Tests\Core\Fixtures\DateTimeBuilder;
use App\Tests\Helpers\DateTimeAssertions;
use App\Tests\Planning\Fixtures\MovieBuilder;
use PHPUnit\Framework\TestCase;

class MovieUnitTest extends TestCase
{
    use DateTimeAssertions;

    public function testSchedule(): void
    {
        // Arrange:
        $movie = MovieBuilder::create()
            ->withDuration(Duration::minutes(120))
            ->build();

        $startTime = DateTime::parse('2024-01-01T20:00:00Z');

        // Act:
        $time = $movie->schedule($startTime);

        // Assert:
        $this->assertDateTimeEquals($startTime, $time->start);
        $this->assertDateTimeEquals('2024-01-01T22:00:00Z', $time->end);
    }

    public function testCannotScheduleUnavailableMovie(): void
    {
        // Arrange:
        $movie = MovieBuilder::create()
            ->unavailable()
            ->build();

        $startTime = DateTimeBuilder::future()->build();

        // Assert:
        $this->expectException(MovieUnavailableException::class);

        // Act:
        $movie->schedule($startTime);
    }
}
