<?php

namespace Tests\Leos\Domain\User\Event;

use Leos\Domain\User\Event\UserEmailWasChanged;
use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class UserEmailWasChangedTest extends TestCase
{
    public function testCreatedEventExposeEmail()
    {
        $event = UserEmailWasChanged::with(UserId::new(), $email = new Email('j@j.com'));

        self::assertEquals($email, $event->email());
    }
}
