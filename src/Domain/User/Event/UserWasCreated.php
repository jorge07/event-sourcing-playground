<?php

namespace Leos\Domain\User\Event;

use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;
use Prooph\EventSourcing\AggregateChanged;

final class UserWasCreated extends AggregateChanged
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var Username
     */
    private $username;

    /**
     * @var \DateTimeImmutable
     */
    private $signUpAt;

    public static function with(UserId $userId, Username $username, Email $email, \DateTimeImmutable $signUpAt): self
    {
        /** @var self $event */
        $event = self::occur($userId->toString(), [
            'username' => $username->__toString(),
            'email' =>  $email->__toString(),
            'signUpAt' =>  $signUpAt->format('U.u'),
        ]);

        $event->username = $username;
        $event->email = $email;
        $event->signUpAt = $signUpAt;

        return $event;
    }

    public function username(): Username
    {
        if (null === $this->username) {

            return new Username($this->payload['username']);
        }

        return $this->username;
    }

    public function email(): Email
    {
        if (null === $this->email) {

            return new Email($this->payload['email']);
        }

        return $this->email;
    }

    public function signUpAt(): \DateTimeImmutable
    {
        if (null === $this->signUpAt) {

            return \DateTimeImmutable::createFromFormat('U.u', $this->payload['signUpAt']);
        }

        return $this->signUpAt;
    }
}
