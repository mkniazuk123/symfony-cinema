<?php

namespace App\Tests\Catalog\Domain\Entities;

use App\Catalog\Domain\Events\MovieArchived;
use App\Catalog\Domain\Events\MovieCreated;
use App\Catalog\Domain\Events\MovieDetailsUpdated;
use App\Catalog\Domain\Events\MovieLengthUpdated;
use App\Catalog\Domain\Events\MovieReleased;
use App\Catalog\Domain\Exceptions\InvalidMovieStatusException;
use App\Catalog\Domain\Values\MovieLength;
use App\Catalog\Domain\Values\MovieStatus;
use App\Tests\Catalog\Fixtures\MovieBuilder;
use App\Tests\Catalog\Fixtures\MovieDetailsBuilder;
use App\Tests\Helpers\DomainEventsHelper;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class MovieUnitTest extends TestCase
{
    public function testMovieIsCreatedWithUpcomingStatus(): void
    {
        // Act:
        $movie = MovieBuilder::create()->build();

        // Assert:
        $this->assertEquals(MovieStatus::UPCOMING, $movie->getStatus());

        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $movieCreated = $domainEvents->getDomainEvent(MovieCreated::class);
        $this->assertEquals(MovieStatus::UPCOMING, $movieCreated->status);
    }

    public function testReconstitute(): void
    {
        // Act:
        $movie = MovieBuilder::reconstitute()->build();

        // Assert:
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsNotDispatched(MovieCreated::class);
    }

    public function testRelease(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->upcoming()->build();

        // Act:
        $movie->release();

        // Assert:
        $this->assertEquals(MovieStatus::RELEASED, $movie->getStatus());
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsDispatched(MovieReleased::class);
    }

    #[TestWith([MovieStatus::RELEASED], 'available')]
    #[TestWith([MovieStatus::ARCHIVED], 'archived')]
    public function testCannotReleaseMovie(MovieStatus $status): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->withStatus($status)->build();

        // Assert:
        $this->expectException(InvalidMovieStatusException::class);

        // Act:
        $movie->release();
    }

    #[TestWith([MovieStatus::UPCOMING], 'upcoming')]
    #[TestWith([MovieStatus::RELEASED], 'available')]
    public function testArchive(MovieStatus $status): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->withStatus($status)->build();

        // Act:
        $movie->archive();

        // Assert:
        $this->assertEquals(MovieStatus::ARCHIVED, $movie->getStatus());
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsDispatched(MovieArchived::class);
    }

    public function testCannotArchiveMovieAgain(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->archived()->build();

        // Assert:
        $this->expectException(InvalidMovieStatusException::class);

        // Act:
        $movie->archive();
    }

    #[TestWith([MovieStatus::UPCOMING], 'upcoming')]
    #[TestWith([MovieStatus::RELEASED], 'available')]
    public function testUpdateDetails(MovieStatus $status): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->withStatus($status)->build();
        $newDetails = MovieDetailsBuilder::create()
            ->withTitle('New Title')
            ->withDescription('New Description')
            ->build();

        // Act:
        $movie->updateDetails($newDetails);

        // Assert:
        $this->assertEquals($newDetails, $movie->getDetails());
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsDispatched(MovieDetailsUpdated::class);
    }

    public function testUpdateDetailsUsingTheSameDetails(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->upcoming()->build();
        $sameDetails = $movie->getDetails();

        // Act:
        $movie->updateDetails($sameDetails);

        // Assert:
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsNotDispatched(MovieDetailsUpdated::class);
    }

    public function testCannotUpdateDetailsOfAnArchivedMovie(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->archived()->build();
        $newDetails = MovieDetailsBuilder::create()->build();

        // Assert:
        $this->expectException(InvalidMovieStatusException::class);

        // Act:
        $movie->updateDetails($newDetails);
    }

    public function testUpdateLength(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->upcoming()->build();
        $newLength = new MovieLength(153);

        // Act:
        $movie->updateLength($newLength);

        // Assert:
        $this->assertEquals($newLength, $movie->getLength());
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsDispatched(MovieLengthUpdated::class);
    }

    public function testUpdateLengthUsingTheSameLength(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->upcoming()->build();
        $sameLength = $movie->getLength();

        // Act:
        $movie->updateLength($sameLength);

        // Assert:
        $domainEvents = DomainEventsHelper::forAggregateRoot($movie);
        $domainEvents->assertDomainEventIsNotDispatched(MovieLengthUpdated::class);
    }

    public function testCannotUpdateLengthOfAnArchivedMovie(): void
    {
        // Arrange:
        $movie = MovieBuilder::reconstitute()->archived()->build();
        $newLength = new MovieLength(140);

        // Assert:
        $this->expectException(InvalidMovieStatusException::class);

        // Act:
        $movie->updateLength($newLength);
    }
}
