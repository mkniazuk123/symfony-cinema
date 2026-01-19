<?php

namespace App\Facilities\Application\Command\Handler;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Application\Command\ArchiveHallCommand;
use App\Facilities\Application\Command\CloseHallCommand;
use App\Facilities\Application\Command\CreateHallCommand;
use App\Facilities\Application\Command\OpenHallCommand;
use App\Facilities\Application\Command\RenameHallCommand;
use App\Facilities\Application\Command\UpdateHallLayoutCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Values\HallId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class HallCommandHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    /**
     * @throws InvalidLayoutException
     */
    #[AsMessageHandler]
    public function createHall(CreateHallCommand $command): HallId
    {
        try {
            $layout = $command->layout->toDomain();
        } catch (InvalidValueException $exception) {
            throw new InvalidLayoutException($exception->getMessage());
        }

        $hall = Hall::create($command->id, $command->name, $layout);
        $this->hallRepository->save($hall);

        return $hall->getId();
    }

    /**
     * @throws HallNotFoundException
     */
    #[AsMessageHandler]
    public function renameHall(RenameHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->rename($command->name);
        $this->hallRepository->save($hall);
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidLayoutException
     * @throws InvalidHallStatusException
     */
    #[AsMessageHandler]
    public function updateHallLayout(UpdateHallLayoutCommand $command): void
    {
        try {
            $layout = $command->layout->toDomain();
        } catch (InvalidValueException $exception) {
            throw new InvalidLayoutException($exception->getMessage());
        }

        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->updateLayout($layout);
        $this->hallRepository->save($hall);
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidHallStatusException
     */
    #[AsMessageHandler]
    public function closeHall(CloseHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->close();
        $this->hallRepository->save($hall);
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidHallStatusException
     */
    #[AsMessageHandler]
    public function openHall(OpenHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->open();
        $this->hallRepository->save($hall);
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidHallStatusException
     */
    #[AsMessageHandler]
    public function archiveHall(ArchiveHallCommand $command): void
    {
        $hall = $this->hallRepository->find($command->id) ?? throw new HallNotFoundException($command->id);
        $hall->archive();
        $this->hallRepository->save($hall);
    }
}
