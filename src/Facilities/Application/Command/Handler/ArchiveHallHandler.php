<?php

namespace App\Facilities\Application\Command\Handler;

use App\Facilities\Application\Command\ArchiveHallCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Ports\HallRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ArchiveHallHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidHallStatusException
     */
    public function __invoke(ArchiveHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->archive();
        $this->hallRepository->save($hall);
    }
}
