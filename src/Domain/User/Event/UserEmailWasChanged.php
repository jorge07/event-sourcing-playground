<?php

namespace Leos\Domain\User\Event;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Prooph\EventSourcing\AggregateChanged;

final class UserEmailWasChanged extends AggregateChanged
{
    /**
     * @var Email
     */
    private $email;

    public static function with(UserId $userId, Email $email): self
    {
        /** @var self $event */
        $event = self::occur($userId->toString(), [
            'email' => $email->__toString()
        ]);

        $event->email = $email;

        return $event;
    }

    public function email(): Email
    {
        if (null === $this->email) {

            return new Email($this->payload['email']);
        }

        return $this->email;
    }
}
