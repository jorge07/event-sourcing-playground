<?php

namespace Leos\Application\Command\User\Handler;

use Leos\Application\Command\User\SignUpCommand;
use Leos\Domain\User\Aggregate\User;

class SignUpHandler
{

    public function __invoke(SignUpCommand $command): void
    {
        User::create(
            $command->uuid,
            $command->username,
            $command->email
        );
    }
}
