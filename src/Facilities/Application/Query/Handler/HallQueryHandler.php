<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Model\HallDto;
use App\Facilities\Application\Model\HallListDto;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Application\Query\GetHallLayoutQuery;
use App\Facilities\Application\Query\GetHallQuery;
use App\Facilities\Application\Query\GetHallsQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class HallQueryHandler
{
    public function __construct(
        private HallReadModel $hallReadModel,
    ) {
    }

    /**
     * @throws HallNotFoundException
     */
    #[AsMessageHandler]
    public function getHall(GetHallQuery $query): HallDto
    {
        return $this->hallReadModel->readHall($query->id) ?? throw new HallNotFoundException($query->id);
    }

    /**
     * @throws HallNotFoundException
     */
    #[AsMessageHandler]
    public function getHallLayout(GetHallLayoutQuery $query): SeatingLayoutDto
    {
        return $this->hallReadModel->readHallLayout($query->id) ?? throw new HallNotFoundException($query->id);
    }

    #[AsMessageHandler]
    public function getHalls(GetHallsQuery $query): HallListDto
    {
        return $this->hallReadModel->readHalls();
    }
}
