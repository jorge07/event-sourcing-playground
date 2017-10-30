<?php

namespace Leos\Application\Command\User\Handler;

use Leos\Application\Command\User\SignUpCommand;

use Leos\Domain\Shared\Exception\ConflictException;
use Leos\Domain\Shared\Exception\NotFoundException;
use Leos\Domain\User\Aggregate\User;
use Leos\Domain\User\Repository\UserRepositoryInterface;

class SignUpHandler
{

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(SignUpCommand $command): void
    {
        try {

            $this->userRepository->get($command->uuid);

            throw new ConflictException("User already exist");

        } catch (NotFoundException $exception) {

            $this->userRepository->store(
                User::create(
                    $command->uuid,
                    $command->username,
                    $command->email
                )
            );
        }
    }
}
