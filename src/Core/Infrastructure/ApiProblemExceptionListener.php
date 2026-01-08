<?php

namespace App\Core\Infrastructure;

use App\Core\Application\ApiProblemException;
use App\Core\Interfaces\ApiProblems\ApiProblem;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProblemExceptionListener
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    #[AsEventListener(event: 'kernel.exception', priority: 1)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ApiProblemException) {
            $event->setResponse($this->createResponseFromApiProblem($exception->apiProblem));
        }
    }

    private function createResponseFromApiProblem(ApiProblem $apiProblem): Response
    {
        return new JsonResponse(
            data: $this->serializer->serialize($apiProblem->toArray(), 'json'),
            status: $apiProblem->status ?? 400,
            json: true
        );
    }
}
