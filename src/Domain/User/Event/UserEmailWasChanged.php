<?php

namespace Leos\Domain\User\Event;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Prooph\EventSourcing\AggregateChanged;

class UserEmailWasChanged extends AggregateChanged
{
    public static function with(UserId $userId, Email $email): self
    {
        /** @var self $event */
        $event = self::occur($userId->toString(), [
            'email' => $email->__toString()
        ]);

        return $event;
    }

    public function email(): Email
    {
        return new Email($this->payload['email']);
    }
}
