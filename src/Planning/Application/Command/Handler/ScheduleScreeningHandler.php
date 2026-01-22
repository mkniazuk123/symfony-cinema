<?php

namespace App\Planning\Application\Command\Handler;

use App\Planning\Application\Command\ScheduleScreening;
use App\Planning\Application\Exceptions\HallNotFoundException;
use App\Planning\Application\Exceptions\MovieNotFoundException;
use App\Planning\Domain\Exceptions\HallClosedException;
use App\Planning\Domain\Exceptions\InvalidTimeException;
use App\Planning\Domain\Exceptions\MovieUnavailableException;
use App\Planning\Domain\Exceptions\TimeConflictException;
use App\Planning\Domain\Ports\HallRepository;
use App\Planning\Domain\Ports\MovieRepository;
use App\Planning\Domain\Services\SchedulerService;
use App\Planning\Domain\Values\ScreeningId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class ScheduleScreeningHandler
{
    public function __construct(
        private MovieRepository $movieRepository,
        private HallRepository $hallRepository,
        private SchedulerService $schedulerService,
    ) {
    }

    /**
     * @throws HallNotFoundException
     * @throws MovieNotFoundException
     * @throws MovieUnavailableException
     * @throws HallClosedException
     * @throws InvalidTimeException
     * @throws TimeConflictException
     */
    #[AsMessageHandler]
    public function scheduleScreening(ScheduleScreening $command): ScreeningId
    {
        $movie = $this->movieRepository->find($command->movieId) ?? throw new MovieNotFoundException($command->movieId);
        $hall = $this->hallRepository->find($command->hallId) ?? throw new HallNotFoundException($command->hallId);
        $screening = $hall->createScreening($command->id, $movie, $command->startTime);

        $this->schedulerService->scheduleScreening($screening);

        return $screening->getId();
    }
}
