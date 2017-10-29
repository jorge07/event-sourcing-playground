<?php

namespace Tests\Leos\Domain\User\Event;

use Leos\Domain\User\Event\UserWasCreated;
use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;

class UserWasCreatedTest extends TestCase
{

    public function testCreatedEventShouldExposeUsernameAndEmail()
    {
        $event = UserWasCreated::with(UserId::new(), $username = new Username('jorge'), $email = new Email('j@j.com'));

        self::assertSame($username, $event->username());
        self::assertSame($email, $event->email());
    }
}
