<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Model\SeatingLayoutDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Application\Query\GetHallLayoutQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetHallLayoutHandler
{
    public function __construct(
        private HallReadModel $hallReadModel,
    ) {
    }

    /**
     * @throws HallNotFoundException
     */
    public function __invoke(GetHallLayoutQuery $query): SeatingLayoutDto
    {
        return $this->hallReadModel->readHallLayout($query->id) ?? throw new HallNotFoundException($query->id);
    }
}
