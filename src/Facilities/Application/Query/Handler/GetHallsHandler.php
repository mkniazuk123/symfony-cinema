<?php

namespace App\Facilities\Application\Query\Handler;

use App\Facilities\Application\Model\HallListDto;
use App\Facilities\Application\Ports\HallReadModel;
use App\Facilities\Application\Query\GetHallsQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetHallsHandler
{
    public function __construct(
        private HallReadModel $hallReadModel,
    ) {
    }

    public function __invoke(GetHallsQuery $query): HallListDto
    {
        return $this->hallReadModel->readHalls();
    }
}
