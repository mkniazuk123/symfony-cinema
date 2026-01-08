<?php

namespace App\Core\Application;

use App\Core\Interfaces\ApiProblems\ApiProblem;

class ApiProblemException extends \RuntimeException
{
    public function __construct(
        public readonly ApiProblem $apiProblem,
    ) {
        parent::__construct(sprintf('%s: %s', $apiProblem->type, $apiProblem->title));
    }
}
