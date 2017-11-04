<?php

namespace Leos\UI\API\Controller\Monitor;

use Symfony\Component\HttpFoundation\JsonResponse;

final class Ping
{
    public function __invoke(): JsonResponse
    {
        return JsonResponse::create('pong');
    }
}
