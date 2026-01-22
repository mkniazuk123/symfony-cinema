<?php

namespace App\Facilities\Application\Command\Handler;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Application\Command\CreateHallCommand;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Values\HallId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateHallHandler
{
    public function __construct(
        private HallRepository $hallRepository,
    ) {
    }

    /**
     * @throws InvalidLayoutException
     */
    public function __invoke(CreateHallCommand $command): HallId
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
}
