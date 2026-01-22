<?php

namespace App\Facilities\Application\Command\Handler;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Application\Command\UpdateHallLayoutCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Ports\HallRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateHallLayoutHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidLayoutException
     * @throws InvalidHallStatusException
     */
    public function __invoke(UpdateHallLayoutCommand $command): void
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
}
