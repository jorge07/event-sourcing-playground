<?php

namespace Tests\Leos\UI\API\EventListener;

use Leos\Domain\Shared\Exception\ConflictException;
use Leos\UI\API\EventListener\ExceptionListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ExceptionListenerTest extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testMustModifyEventWithAJsonResponse()
    {
        $event = $this->kernelException(new ConflictException("Conflict happened"));

        $response = $event->getResponse();

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    public function testConflictExceptionMustConvertInto409()
    {
        $message = "Conflict happened";

        $event = $this->kernelException(
            new ConflictException($message)
        );

        $response = $event->getResponse();

        self::assertEquals(409, $response->getStatusCode());
        self::assertContains($message, $response->getContent());
    }

    public function testInvalidArgumentExceptionMustConvertInto400()
    {
        $message = "Not Valid";

        $event = $this->kernelException(
            new \InvalidArgumentException($message)
        );

        $response = $event->getResponse();

        self::assertEquals(400, $response->getStatusCode());
        self::assertContains($message, $response->getContent());
    }

    private function kernelException(\Exception $exception): GetResponseForExceptionEvent
    {
        $event = new GetResponseForExceptionEvent(
            static::$kernel,
            Request::create('/api/ping'),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ExceptionListener())
            ->onKernelException($event)
        ;

        return $event;
    }

}
