<?php

namespace Tests\Leos\Application\Command\Balance;

use Leos\Application\Command\Balance\DepositCommand;
use Leos\Application\Command\Balance\Handler\DepositHandler;
use Leos\Domain\Balance\ValueObject\BalanceOwnerId;

use Leos\Domain\Balance\Aggregate\Balance;
use Leos\Domain\Balance\Repository\BalanceRepositoryInterface;
use Leos\Domain\User\Aggregate\User;

use Money\Currency;
use PHPUnit\Framework\TestCase;
use Tests\Leos\Domain\User\Aggregate\UserTest;

class DepositHandlerTest extends TestCase implements BalanceRepositoryInterface
{
    /** @var User|null */
    protected $user;

    /** @var DepositHandler|null */
    protected $handler;

    /** @var Balance|null */
    protected $balance;

    protected function setUp()
    {
        $this->user = UserTest::create();

        $this->balance = Balance::create($this->user->uuid()->toString(), new Currency('EUR'));

        $this->handler = new DepositHandler($this);
    }

    protected function tearDown()
    {
        $this->user = null;
        $this->balance = null;
        $this->handler = null;
    }

    public function testDepositCommand()
    {
        $this->handler->handle(new DepositCommand($this->user->uuid()->toString(), '10', 'EUR'));

        self::assertEquals(10, $this->balance->amount()->getAmount());
        self::assertEquals('EUR', $this->balance->amount()->getCurrency()->getCode());
    }

    public function oneFromOwner(BalanceOwnerId $balanceId): Balance
    {
        return $this->balance;
    }

    public function store(Balance $balance): void
    {
    }
}
