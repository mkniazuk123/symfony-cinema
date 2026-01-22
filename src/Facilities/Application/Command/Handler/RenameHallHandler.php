<?php

namespace App\Facilities\Application\Command\Handler;

use App\Facilities\Application\Command\RenameHallCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Domain\Ports\HallRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RenameHallHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    /**
     * @throws HallNotFoundException
     */
    public function __invoke(RenameHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->rename($command->name);
        $this->hallRepository->save($hall);
    }
}
