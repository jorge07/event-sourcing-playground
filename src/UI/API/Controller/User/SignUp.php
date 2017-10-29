<?php

namespace Leos\UI\API\Controller\User;

use Leos\Application\Command\User\Handler\SignUpHandler;
use Leos\Application\Command\User\SignUpCommand;
use Leos\Domain\User\ValueObject\UserId;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SignUp
{
    /**
     * @var SignUpHandler
     */
    private $handler;

    public function __construct(SignUpHandler $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $command = new SignUpCommand(
            UserId::new()->toString(),
            $request->get('username'),
            $request->get('email')
        );

        $this->handler->__invoke($command);

        return JsonResponse::create([
            'uuid' => $command->uuid->toString(),
            'username' => $command->username->__toString(),
            'email' => $command->email->__toString()
        ]);
    }
}
