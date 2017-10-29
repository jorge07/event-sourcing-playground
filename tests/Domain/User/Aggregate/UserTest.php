<?php

namespace Tests\Leos\Domain\User\Aggregate;

use Assert\InvalidArgumentException;
use Leos\Domain\User\Aggregate\User;
use Leos\Domain\User\Event\UserEmailWasChanged;
use Leos\Domain\User\Event\UserWasCreated;
use Leos\Domain\User\ValueObject\Email;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Domain\User\ValueObject\Username;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser(): User
    {
        $user = User::create(UserId::new(), new Username('jorge'), new Email('jorge.arcoma@gmail.com'));

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('jorge', $user->username());
        self::assertEquals('jorge.arcoma@gmail.com', $user->email());

        return $user;
    }

    public function testReconstructUserFromEvents()
    {
        /** @var UserId $userId */
        $userId = UserId::new();

        $user = User::fromEvents(
            new \ArrayIterator(
                [
                    UserWasCreated::with($userId, new Username('jorge'), new Email('jorge.arcoma@gmail.io')),
                    UserEmailWasChanged::with($userId, new Email('jorge.arcoma@gmail.com'))
                ]
            )
        );

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('jorge', $user->username());
        self::assertEquals('jorge.arcoma@gmail.com', $user->email());
        self::assertInstanceOf(\DateTimeImmutable::class, $user->signedUpAt());
    }

    public function testCreateUserWithInvalidEmailShouldFail()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Email not valid');

        $userId = UserId::new();

        User::create($userId, new Username('jorge'), new Email('jorge.arcoma@gmail'));
    }

    public function testCreateUserWithEmptyUsernameShouldFail()
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Username can\'t be empty');

        $userId = UserId::new();

        User::create($userId, new Username(''), new Email('jorge.arcoma@gmail'));

    }

    public function testChangeEmail()
    {
        $user = $this->testCreateUser();

        $user->changeEmail($email = new Email('lolaso@maximo.io'));

        self::assertSame($user->email(), $email);
    }

    public static function create(): User
    {
        return User::create(UserId::new(), new Username('jorge'), new Email('jorge.arcoma@gmail.com'));
    }
}
