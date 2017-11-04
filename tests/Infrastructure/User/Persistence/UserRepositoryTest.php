<?php

namespace Tests\Leos\Infrastructure\User\Persistence;

use Leos\Domain\User\ValueObject\Email;
use Leos\Infrastructure\User\Persistence\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Leos\Domain\User\Aggregate\UserTest;

class UserRepositoryTest extends KernelTestCase
{
    /** @var UserRepository|null */
    private $userRepo;

    protected function setUp()
    {
        self::bootKernel();

        $this->userRepo = static::$kernel->getContainer()->get('test.user.repo');
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->userRepo = null;
    }

    public function testEventStoreSaveStream()
    {
        $user = UserTest::create();

        $user->changeEmail(new Email('lolaso@maximo.com'));
        $user->changeEmail(new Email('lolaso1@maximo.com'));
        $user->changeEmail(new Email('lolaso2@maximo.com'));
        $user->changeEmail(new Email('lolaso3@maximo.com'));
        $user->changeEmail(new Email('lolaso4@maximo.com'));
        $user->changeEmail(new Email('lolaso5@maximo.com'));
        $user->changeEmail(new Email('lolaso6@maximo.com'));
        $user->changeEmail(new Email('lolaso7@maximo.com'));
        $user->changeEmail(new Email('lolaso8@maximo.com'));
        $user->changeEmail(new Email('lolaso9@maximo.com'));
        $user->changeEmail(new Email('lolaso10@maximo.com'));

        $this->userRepo->saveAggregateRoot($user);

        $this->userRepo->clearIdentityMap();

        $userStored = $this->userRepo->get($user->uuid());

        self::assertEquals($user, $userStored);
        self::assertEquals(12, $this->userRepo->extractAggregateVersion($user));
    }
}