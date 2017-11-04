<?php

namespace Leos\UI\API\EventListener;

use Leos\Domain\Shared\Exception\ConflictException;

use Leos\Domain\Shared\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class ExceptionListener
{
    /**
     * {@inheritdoc}
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = $this->createResponseFromException($exception);

        $event->setResponse($response);
    }

    private function createResponseFromException(\Exception $exception): JsonResponse
    {
        $code = $this->exceptionMapper($exception);

        return JsonResponse::create(['message' => $exception->getMessage()], $code);
    }

    private function exceptionMapper(\Exception $exception): int
    {
        switch (true) {
            case $exception instanceOf ConflictException:
                return 409;
            case $exception instanceOf NotFoundException:
                return 404;
            case $exception instanceOf \InvalidArgumentException:
                return 400;
            default:
                return 500;
        }
    }
}