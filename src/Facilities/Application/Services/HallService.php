<?php

namespace App\Facilities\Application\Services;

use App\Core\Domain\InvalidValueException;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Application\Model\HallDto;
use App\Facilities\Application\Model\HallListDto;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Domain\Entities\Hall;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Ports\HallRepository;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Domain\Values\HallName;
use Doctrine\DBAL\Connection;

class HallService
{
    public function __construct(
        private Connection $connection,
        private HallRepository $hallRepository,
        private HallReadModel $hallReadModel,
    ) {
    }

    /**
     * @throws HallNotFoundException
     */
    public function getHall(HallId $id): HallDto
    {
        return $this->hallReadModel->readHall($id) ?? throw new HallNotFoundException($id);
    }

    /**
     * @throws HallNotFoundException
     */
    public function getHallLayout(HallId $id): SeatingLayoutDto
    {
        return $this->hallReadModel->readHallLayout($id) ?? throw new HallNotFoundException($id);
    }

    public function getHalls(): HallListDto
    {
        return $this->hallReadModel->readHalls();
    }

    /**
     * @throws InvalidLayoutException
     */
    public function createHall(HallName $name, SeatingLayoutDto $layout): HallId
    {
        try {
            $layout = $layout->toDomain();
        } catch (InvalidValueException $exception) {
            throw new InvalidLayoutException($exception->getMessage());
        }

        return $this->connection->transactional(function () use ($name, $layout) {
            $hall = Hall::create($name, $layout);
            $this->hallRepository->save($hall);

            return $hall->getId();
        });
    }

    public function renameHall(HallId $id, HallName $name): void
    {
        $this->connection->transactional(function () use ($id, $name) {
            $hall = $this->hallRepository->find($id) ?? throw new HallNotFoundException($id);
            $hall->rename($name);
            $this->hallRepository->save($hall);
        });
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidLayoutException
     * @throws InvalidHallStatusException
     */
    public function updateHallLayout(HallId $id, SeatingLayoutDto $layout): void
    {
        try {
            $layout = $layout->toDomain();
        } catch (InvalidValueException $exception) {
            throw new InvalidLayoutException($exception->getMessage());
        }

        $this->connection->transactional(function () use ($id, $layout) {
            $hall = $this->hallRepository->find($id) ?? throw new HallNotFoundException($id);
            $hall->updateLayout($layout);
            $this->hallRepository->save($hall);
        });
    }

    /**
     * @throws HallNotFoundException
     * @throws InvalidHallStatusException
     */
    public function archiveHall(HallId $id): void
    {
        $this->connection->transactional(function () use ($id) {
            $hall = $this->hallRepository->find($id) ?? throw new HallNotFoundException($id);
            $hall->archive();
            $this->hallRepository->save($hall);
        });
    }
}
