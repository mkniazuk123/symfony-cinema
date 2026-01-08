<?php

namespace App\Core\Interfaces\Controllers;

use App\Core\Application\ApiProblemException;
use App\Core\Interfaces\ApiProblems\ApiProblem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class ApiController
{
    private SerializerInterface $serializer;

    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    protected function jsonResponse(mixed $data, int $status = 200): JsonResponse
    {
        $serializedData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($serializedData, $status, json: true);
    }

    /**
     * @throws ApiProblemException
     */
    protected function apiProblem(ApiProblem $apiProblem): never
    {
        throw new ApiProblemException($apiProblem);
    }
}
