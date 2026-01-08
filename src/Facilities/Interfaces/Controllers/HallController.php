<?php

namespace App\Facilities\Interfaces\Controllers;

use App\Core\Interfaces\Controllers\ApiController;
use App\Facilities\Application\Exceptions\HallNotFoundException;
use App\Facilities\Application\Exceptions\InvalidLayoutException;
use App\Facilities\Application\Services\HallService;
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
    public function __construct(private HallService $service)
    {
    }

    #[Route(methods: ['POST'])]
    public function createHall(#[MapRequestPayload] CreateHallRequest $request): Response
    {
        $data = $request->resolve();
        $name = $data['name'];
        $layout = $data['layout'];

        try {
            $hallId = $this->service->createHall($name, $layout);
        } catch (InvalidLayoutException $exception) {
            $this->apiProblem(InvalidLayoutApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($hallId);

        return $this->jsonResponse($hall, status: 201);
    }

    #[Route(methods: ['GET'])]
    public function getHalls(): Response
    {
        $halls = $this->service->getHalls();

        return $this->jsonResponse($halls);
    }

    #[Route(path: '/{id}', methods: ['GET'])]
    public function getHall(HallId $id): Response
    {
        try {
            $hall = $this->service->getHall($id);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        }

        return $this->jsonResponse($hall);
    }

    #[Route(path: '/{id}/layout', methods: ['GET'])]
    public function getHallLayout(HallId $id): Response
    {
        try {
            $layout = $this->service->getHallLayout($id);
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
            $this->service->renameHall($id, $name);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($id);

        return $this->jsonResponse($hall);
    }

    #[Route(path: '/{id}/layout', methods: ['PUT'])]
    public function updateHallLayout(
        HallId $id,
        #[MapRequestPayload] UpdateHallLayoutRequest $request,
    ): Response {
        $data = $request->resolve();
        $layout = $data['layout'];

        try {
            $this->service->updateHallLayout($id, $layout);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidLayoutException $exception) {
            $this->apiProblem(InvalidLayoutApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($id);

        return $this->jsonResponse($hall);
    }

    #[Route(path: '/{id}/close', methods: ['POST'])]
    public function closeHall(HallId $id): Response
    {
        try {
            $this->service->closeHall($id);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($id);

        return $this->jsonResponse($hall);
    }

    #[Route(path: '/{id}/open', methods: ['POST'])]
    public function openHall(HallId $id): Response
    {
        try {
            $this->service->openHall($id);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($id);

        return $this->jsonResponse($hall);
    }

    #[Route(path: '/{id}/archive', methods: ['POST'])]
    public function archiveHall(HallId $id): Response
    {
        try {
            $this->service->archiveHall($id);
        } catch (HallNotFoundException $exception) {
            $this->apiProblem(HallNotFoundApiProblem::fromException($exception));
        } catch (InvalidHallStatusException $exception) {
            $this->apiProblem(InvalidHallStatusApiProblem::fromException($exception));
        }

        $hall = $this->service->getHall($id);

        return $this->jsonResponse($hall);
    }
}
