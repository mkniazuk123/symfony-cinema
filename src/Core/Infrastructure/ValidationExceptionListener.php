<?php

namespace App\Core\Infrastructure;

use App\Core\Application\ApiProblemException;
use App\Core\Interfaces\ApiProblems\ValidationFailedApiProblem;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionListener
{
    #[AsEventListener(event: 'kernel.exception', priority: 1)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof UnprocessableEntityHttpException) {
            if (($exception = $exception->getPrevious()) instanceof ValidationFailedException) {
                throw new ApiProblemException(ValidationFailedApiProblem::fromViolationList($exception->getViolations(), 422));
            }
        } elseif ($exception instanceof BadRequestHttpException) {
            if (($exception = $exception->getPrevious()) instanceof ValidationFailedException) {
                throw new ApiProblemException(ValidationFailedApiProblem::fromViolationList($exception->getViolations(), 400));
            }
        }
    }
}
