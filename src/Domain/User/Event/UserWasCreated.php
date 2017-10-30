<?php

namespace Leos\Domain\User\Event;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;
use Prooph\EventSourcing\AggregateChanged;

class UserWasCreated extends AggregateChanged
{
    public static function with(UserId $userId, Username $username, Email $email, \DateTimeImmutable $signUpAt): self
    {
        /** @var self $event */
        $event = self::occur($userId->toString(), [
            'username' => $username->__toString(),
            'email' =>  $email->__toString(),
            'signUpAt' =>  $signUpAt->getTimestamp()
        ]);

        return $event;
    }

    public function username(): Username
    {
        return new Username($this->payload['username']);
    }

    public function email(): Email
    {
        return new Email($this->payload['email']);
    }

    public function signUpAt(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('U', $this->payload['signUpAt']);
    }
}
