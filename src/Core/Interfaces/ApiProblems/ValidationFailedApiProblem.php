<?php

namespace App\Core\Interfaces\ApiProblems;

use Symfony\Component\Validator\ConstraintViolationListInterface;

readonly class ValidationFailedApiProblem extends ApiProblem
{
    public static function fromViolationList(ConstraintViolationListInterface $violationList, int $status): self
    {
        $errors = array_map(
            fn ($violation) => [
                'property' => $violation->getPropertyPath(),
                'error' => $violation->getMessage(),
            ],
            iterator_to_array($violationList)
        );

        return self::fromArray([
            'type' => 'validationFailed',
            'title' => 'Validation failed',
            'status' => $status,
            'errors' => $errors,
        ]);
    }
}
