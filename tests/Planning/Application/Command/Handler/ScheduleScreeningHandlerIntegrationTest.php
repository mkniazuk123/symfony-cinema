<?php

namespace App\Tests\Planning\Application\Command\Handler;

use App\Core\Domain\DateTimeRange;
use App\Core\Domain\Duration;
use App\Planning\Application\Command\ScheduleScreening;
use App\Planning\Application\Exceptions\HallNotFoundException;
use App\Planning\Application\Exceptions\MovieNotFoundException;
use App\Planning\Domain\Exceptions\HallClosedException;
use App\Planning\Domain\Exceptions\InsufficientTimeException;
use App\Planning\Domain\Exceptions\MovieUnavailableException;
use App\Planning\Domain\Exceptions\TimeConflictException;
use App\Planning\Domain\Ports\HallRepository;
use App\Planning\Domain\Ports\MovieRepository;
use App\Planning\Domain\Ports\ScreeningRepository;
use App\Planning\Domain\Values\MovieId;
use App\Planning\Domain\Values\ScreeningId;
use App\Tests\Core\Fixtures\DateTimeBuilder;
use App\Tests\IntegrationTestCase;
use App\Tests\Planning\Fixtures\HallReconstituteBuilder;
use App\Tests\Planning\Fixtures\MovieReconstituteBuilder;
use App\Tests\Planning\Fixtures\ScreeningReconsituteBuilder;

class ScheduleScreeningHandlerIntegrationTest extends IntegrationTestCase
{
    private HallRepository $hallRepository;
    private MovieRepository $movieRepository;
    private ScreeningRepository $screeningRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();
        $this->hallRepository = $container->get(HallRepository::class);
        $this->movieRepository = $container->get(MovieRepository::class);
        $this->screeningRepository = $container->get(ScreeningRepository::class);
    }

    public function testCannotScheduleScreeningOfNonexistentMovie(): void
    {
        // Arrange:
        $id = ScreeningId::generate();
        $movieId = MovieId::generate();
        $hall = HallReconstituteBuilder::create()->build();
        $this->hallRepository->save($hall);

        // Assert:
        $this->expectException(MovieNotFoundException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movieId, DateTimeBuilder::future()->build())
        );
    }

    public function testCannotScheduleScreeningInNonexistentHall(): void
    {
        // Arrange:
        $id = ScreeningId::generate();
        $movie = MovieReconstituteBuilder::create()->build();
        $this->movieRepository->save($movie);
        $hallId = HallReconstituteBuilder::create()->build()->getId();

        // Assert:
        $this->expectException(HallNotFoundException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hallId, $movie->getId(), DateTimeBuilder::future()->build())
        );
    }

    public function testCannotScheduleScreeningInClosedHall(): void
    {
        // Arrange:
        $id = ScreeningId::generate();
        $movie = MovieReconstituteBuilder::create()->build();
        $this->movieRepository->save($movie);
        $hall = HallReconstituteBuilder::create()->closed()->build();
        $this->hallRepository->save($hall);

        // Assert:
        $this->expectException(HallClosedException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movie->getId(), DateTimeBuilder::future()->build())
        );
    }

    public function testCannotScheduleUnavailableMovie(): void
    {
        // Arrange:
        $id = ScreeningId::generate();
        $movie = MovieReconstituteBuilder::create()->unavailable()->build();
        $this->movieRepository->save($movie);
        $hall = HallReconstituteBuilder::create()->build();
        $this->hallRepository->save($hall);

        // Assert:
        $this->expectException(MovieUnavailableException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movie->getId(), DateTimeBuilder::future()->build())
        );
    }

    public function testCannotScheduleScreeningLessThanOneDayInAdvance(): void
    {
        // Arrange:
        $id = ScreeningId::generate();
        $movie = MovieReconstituteBuilder::create()->build();
        $this->movieRepository->save($movie);
        $hall = HallReconstituteBuilder::create()->build();
        $this->hallRepository->save($hall);
        $time = DateTimeBuilder::future()->inHours(23)->build();

        // Assert:
        $this->expectException(InsufficientTimeException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movie->getId(), $time)
        );
    }

    public function testCannotScheduleConflictScreening(): void
    {
        // Arrange:
        $id = ScreeningId::generate();

        $movie = MovieReconstituteBuilder::create()->build();
        $this->movieRepository->save($movie);

        $hall = HallReconstituteBuilder::create()->build();
        $this->hallRepository->save($hall);

        $this->screeningRepository->save(
            ScreeningReconsituteBuilder::create()
                ->withHallId($hall->getId())
                ->withMovieId($movie->getId())
                ->withTime(
                    new DateTimeRange(
                        DateTimeBuilder::future()->inHours(24)->build(),
                        DateTimeBuilder::future()->inHours(26)->build()
                    )
                )
                ->build()
        );
        $time = DateTimeBuilder::future()->inHours(25)->build();

        // Assert:
        $this->expectException(TimeConflictException::class);

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movie->getId(), $time)
        );
    }

    public function testCanScheduleScreening(): void
    {
        // Arrange:
        $id = ScreeningId::generate();

        $movieDuration = Duration::minutes(120);
        $movie = MovieReconstituteBuilder::create()
            ->withDuration($movieDuration)
            ->build();
        $this->movieRepository->save($movie);

        $hall = HallReconstituteBuilder::create()->build();
        $this->hallRepository->save($hall);

        $time = DateTimeBuilder::future()->inDays(2)->build();

        // Act:
        $this->commandBus->dispatch(
            new ScheduleScreening($id, $hall->getId(), $movie->getId(), $time)
        );

        // Assert:
        $screening = $this->screeningRepository->find($id);
        $this->assertNotNull($screening);
        $this->assertEquals($hall->getId(), $screening->getHallId());
        $this->assertEquals($movie->getId(), $screening->getMovieId());
        $this->assertEquals($time, $screening->getTime()->start);
        $this->assertEquals($time->add($movieDuration), $screening->getTime()->end);
    }
}
