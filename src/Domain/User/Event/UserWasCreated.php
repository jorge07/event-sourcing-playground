<?php

namespace Leos\Domain\User\Event;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;
use Prooph\EventSourcing\AggregateChanged;

class UserWasCreated extends AggregateChanged
{
    public static function with(UserId $userId, Username $username, Email $email): self
    {
        /** @var self $event */
        $event = self::occur($userId->toString(), [
            'username' => $username,
            'email' =>  $email
        ]);

        return $event;
    }

    public function username(): Username
    {
        return $this->payload['username'];
    }

    public function email(): Email
    {
        return $this->payload['email'];
    }
}
