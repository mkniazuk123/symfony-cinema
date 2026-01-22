<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Model\HallDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Application\Query\GetHallQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetHallHandler
{
    public function __construct(
        private HallReadModel $hallReadModel,
    ) {
    }

    /**
     * @throws HallNotFoundException
     */
    public function __invoke(GetHallQuery $query): HallDto
    {
        return $this->hallReadModel->readHall($query->id) ?? throw new HallNotFoundException($query->id);
    }
}
