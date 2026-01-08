<?php

namespace App\Core\Interfaces\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class HealthController
{
    #[Route(path: '/_healthz', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response(content: 'OK', status: Response::HTTP_OK);
    }
}
