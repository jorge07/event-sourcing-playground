<?php

namespace Tests\Leos\Domain\Balance\Aggregate;

use Leos\Domain\Balance\Aggregate\Balance;
use Leos\Domain\Balance\Event\BalanceWasCreated;
use Leos\Domain\Balance\Event\TransactionWasPerform;
use Leos\Domain\Balance\Model\Transaction;
use Leos\Domain\Balance\ValueObject\TransactionId;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Tests\Leos\Domain\User\Aggregate\UserTest;

class BalanceTest extends TestCase
{
    public function testCreateNewBalance(): Balance
    {
        $user = UserTest::create();

        $balance = Balance::create($user->uuid()->toString(), new Currency('EUR'));

        self::assertEquals(0, $balance->amount()->getAmount());
        self::assertEquals('EUR', $balance->amount()->getCurrency()->getCode());

        return $balance;
    }

    public function testDoDeposit(): Balance
    {
        $balance = $this->testCreateNewBalance();

        $amount = new Money(
            10,
            new Currency('EUR')
        );

        $balance->deposit($amount);


        self::assertEquals(10, $balance->amount()->getAmount());
        self::assertEquals('EUR', $balance->amount()->getCurrency()->getCode());

        return $balance;
    }

    public function testDoWithdrawal()
    {
        $balance = $this->testDoDeposit();

        $amount = new Money(
            10,
            new Currency('EUR')
        );

        $balance->withdrawal($amount);

        self::assertEquals(0, $balance->amount()->getAmount());
        self::assertEquals('EUR', $balance->amount()->getCurrency()->getCode());
    }

    public function testGetBalanceFromEvents()
    {
        $user = UserTest::create();

        $userId = $user->uuid();

        $deposit = new Money(
            10,
            new Currency('EUR')
        );

        $withdrawal = new Money(
                5,
                new Currency('EUR')
        );

        $balance = Balance::fromHistory(new \ArrayIterator([
            BalanceWasCreated::new($userId->toString(), new Money(0, new Currency('EUR'))),
            TransactionWasPerform::deposit($userId->toString(), TransactionId::new(), $deposit),
            TransactionWasPerform::withdrawal($userId->toString(), TransactionId::new(), $withdrawal),
        ]));

        self::assertSame($user->uuid()->toString(), $balance->owner()->toString());
        self::assertEquals(5, $balance->amount()->getAmount());
        self::assertEquals('EUR', $balance->amount()->getCurrency()->getCode());
    }

    public function testRollbackDeposit()
    {
        $balance = $this->testCreateNewBalance();

        self::assertEquals(0, $balance->amount()->getAmount());

        $amount = new Money(
            10,
            new Currency('EUR')
        );

        $deposit = $balance->deposit($amount);

        self::assertEquals(10, $balance->amount()->getAmount());

        $balance->rollback($deposit);

        self::assertEquals(0, $balance->amount()->getAmount());

        /** @var TransactionWasPerform[]|BalanceWasCreated[] $events */
        $events = $balance->popRecordedEvents();

        self::assertCount(3, $events);
        self::assertInstanceOf(BalanceWasCreated::class, $events[0]);

        self::assertInstanceOf(TransactionWasPerform::class, $events[1]);
        self::assertEquals(Transaction::TYPES_DEPOSIT, $events[1]->type());

        self::assertInstanceOf(TransactionWasPerform::class, $events[2]);
        self::assertEquals(Transaction::TYPES_ROLLBACK_DEPOSIT, $events[2]->type());

        self::assertEquals($deposit->uuid()->toString(), $events[2]->payload()['transactionToRollbackId']);
    }

    public function testRollbackWithdrawal()
    {
        $balance = $this->testDoDeposit();

        self::assertEquals(10, $balance->amount()->getAmount());

        $amount = new Money(
            10,
            new Currency('EUR')
        );

        $withdrawal = $balance->withdrawal($amount);

        self::assertEquals(0, $balance->amount()->getAmount());
        self::assertEquals('EUR', $balance->amount()->getCurrency()->getCode());

        $rollback = $balance->rollback($withdrawal);

        self::assertEquals(10, $balance->amount()->getAmount());
        self::assertEquals(Transaction::TYPES_ROLLBACK_WITHDRAWAL, $rollback->type());


        /** @var TransactionWasPerform[]|BalanceWasCreated[] $events */
        $events = $balance->popRecordedEvents();

        self::assertCount(4, $events);
        self::assertInstanceOf(BalanceWasCreated::class, $events[0]);

        self::assertInstanceOf(TransactionWasPerform::class, $events[1]);
        self::assertEquals(Transaction::TYPES_DEPOSIT, $events[1]->type());

        self::assertInstanceOf(TransactionWasPerform::class, $events[2]);
        self::assertEquals(Transaction::TYPES_WITHDRAWAL, $events[2]->type());

        self::assertInstanceOf(TransactionWasPerform::class, $events[3]);
        self::assertEquals(Transaction::TYPES_ROLLBACK_WITHDRAWAL, $events[3]->type());
        self::assertEquals($withdrawal->uuid()->toString(), $events[3]->payload()['transactionToRollbackId']);
    }
}
