<?php

namespace App\Facilities\Interfaces\Controllers;

use App\Core\Application\CommandBus;
use App\Core\Application\QueryBus;
use App\Core\Interfaces\Controllers\ApiController;
use App\Facilities\Application\Command\ArchiveHallCommand;
use App\Facilities\Application\Command\CloseHallCommand;
use App\Facilities\Application\Command\CreateHallCommand;
use App\Facilities\Application\Command\OpenHallCommand;
use App\Facilities\Application\Command\RenameHallCommand;
use App\Facilities\Application\Command\UpdateHallLayoutCommand;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Application\Query\GetHallLayoutQuery;
use App\Facilities\Application\Query\GetHallQuery;
use App\Facilities\Application\Query\GetHallsQuery;
use App\Facilities\Domain\Exceptions\InvalidHallStatusException;
use App\Facilities\Domain\Values\HallId;
use App\Facilities\Interfaces\ApiProblems\HallNotFoundApiProblem;
use App\Facilities\Interfaces\ApiProblems\InvalidHallStatusApiProblem;
use App\Facilities\Interfaces\ApiProblems\InvalidLayoutApiProblem;
use App\Facilities\Interfaces\Requests\CreateHallRequest;
use App\Facilities\Interfaces\Requests\RenameHallRequest;
use App\Facilities\Interfaces\Requests\UpdateHallLayoutRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/halls')]
class HallController extends ApiController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    #[Route(methods: ['POST'])]
    public function createHall(#[MapRequestPayload] CreateHallRequest $request): Response
    {
        $data = $request->resolve();
        $id = HallId::generate();
        $name = $data['name'];
        $layout = $data['layout'];

        try {
            $this->commandBus->dispatch(new CreateHallCommand($id, $name, $layout));
        } catch (InvalidLayoutException $exception) {
            $this->apiProblem(InvalidLayoutApiProblem::fromException($exception));
        }

        return $this->sendHall($id, 201);
    }

    #[Route(methods: ['GET'])]
    public function getHalls(): Response
    {
        $halls = $this->queryBus->query(new GetHallsQuery());

        return $this->jsonResponse($halls);
    }

    #[Route(path: '/{id}', methods: ['GET'])]
    public function getHall(HallId $id): Response
    {
        try {
            return $this->sendHall($id);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        }
    }

    #[Route(path: '/{id}/layout', methods: ['GET'])]
    public function getHallLayout(HallId $id): Response
    {
        try {
            $layout = $this->queryBus->query(new GetHallLayoutQuery($id));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        }

        return $this->jsonResponse($layout);
    }

    #[Route(path: '/{id}/name', methods: ['PUT'])]
    public function renameHall(
        HallId $id,
        #[MapRequestPayload] RenameHallRequest $request,
    ): Response {
        $data = $request->resolve();
        $name = $data['name'];

        try {
            $this->commandBus->dispatch(new RenameHallCommand($id, $name));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        return $this->sendHall($id);
    }

    #[Route(path: '/{id}/layout', methods: ['PUT'])]
    public function updateHallLayout(
        HallId $id,
        #[MapRequestPayload] UpdateHallLayoutRequest $request,
    ): Response {
        $data = $request->resolve();
        $layout = $data['layout'];

        try {
            $this->commandBus->dispatch(new UpdateHallLayoutCommand($id, $layout));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidLayoutException $exception) {
            $this->apiProblem(InvalidLayoutApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        return $this->sendHall($id);
    }

    #[Route(path: '/{id}/close', methods: ['POST'])]
    public function closeHall(HallId $id): Response
    {
        try {
            $this->commandBus->dispatch(new CloseHallCommand($id));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        return $this->sendHall($id);
    }

    #[Route(path: '/{id}/open', methods: ['POST'])]
    public function openHall(HallId $id): Response
    {
        try {
            $this->commandBus->dispatch(new OpenHallCommand($id));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        return $this->sendHall($id);
    }

    #[Route(path: '/{id}/archive', methods: ['POST'])]
    public function archiveHall(HallId $id): Response
    {
        try {
            $this->commandBus->dispatch(new ArchiveHallCommand($id));
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        return $this->sendHall($id);
    }

    private function sendHall(HallId $id, int $status = 200): Response
    {
        $hall = $this->queryBus->query(new GetHallQuery($id));

        return $this->jsonResponse($hall, status: $status);
    }
}
