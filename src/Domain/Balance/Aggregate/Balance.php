<?php

namespace Leos\Domain\Balance\Aggregate;

use Assert\Assertion;

use Leos\Domain\Balance\Event\BalanceWasCreated;
use Leos\Domain\Balance\Event\TransactionWasPerform;
use Leos\Domain\Balance\Model\Transaction;
use Leos\Domain\Balance\ValueObject\BalanceOwnerId;

use Money\Currency;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Money\Money;

final class Balance extends AggregateRoot
{
    /**
     * @var BalanceOwnerId
     */
    private $owner;

    /**
     * @var Money
     */
    private $amount;

    public function owner(): BalanceOwnerId
    {
        return $this->owner;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public static function create(string $userId, ?Currency $currency): self
    {
        $balance = new self();

        $balance->recordThat(
            BalanceWasCreated::new($userId, new Money(0, $currency ?: new Currency('EUR')))
        );

        return $balance;
    }

    public function deposit(Money $amount): Transaction
    {
        $transaction = Transaction::deposit($amount);

        $this->recordThat(TransactionWasPerform::deposit($this->aggregateId(), $transaction->uuid(), $amount));

        return $transaction;
    }

    public function withdrawal(Money $amount): Transaction
    {
        $transaction = Transaction::withdrawal($amount);

        $this->recordThat(TransactionWasPerform::withdrawal($this->aggregateId(), $transaction->uuid(), $amount));

        return $transaction;
    }

    public function rollback(Transaction $transaction): Transaction
    {
        $rollback = $transaction->rollback();

        $this->recordThat(TransactionWasPerform::rollback($this->aggregateId(), $rollback->uuid(), $transaction->uuid(), $rollback->type(), $rollback->amount()));

        return $rollback;
    }

    private function whenCreated(BalanceWasCreated $event): void
    {
        $this->owner = BalanceOwnerId::fromString($event->aggregateId());
        $this->amount = $event->amount();
    }

    public function whenDeposit(TransactionWasPerform $event): void
    {
        Assertion::eq(Transaction::TYPES_DEPOSIT, $event->type(), 'Transaction must be a deposit type');
        Assertion::true($this->owner()->eq($event->userId()), 'This transaction is not for that user');

        $this->amount = $this->amount->add($event->amount());
    }

    public function whenRollbackDeposit(TransactionWasPerform $event): void
    {
        Assertion::eq(Transaction::TYPES_ROLLBACK_DEPOSIT, $event->type(), 'Transaction must be a rollback_deposit type');
        Assertion::true($this->owner()->eq($event->userId()), 'This transaction is not for that user');
        Assertion::true(
            $this->amount->greaterThanOrEqual($event->amount()),
            'The balance needs to be greater or equal to the transaction to rollback'
        );

        $this->amount = $this->amount->subtract($event->amount());
    }
    public function whenWithdrawal(TransactionWasPerform $event): void
    {
        $type = $event->type();
        Assertion::eq(Transaction::TYPES_WITHDRAWAL, $type, "Transaction must be a deposit type, $type given.");
        Assertion::true($this->owner()->eq($event->userId()), 'This transaction is not for that user');
        Assertion::true(
            $this->amount->greaterThanOrEqual($event->amount()),
            'The amount of origin must be higher or equal to the argument'
        );

        $this->amount = $this->amount->subtract($event->amount());
    }

    public function whenRollbackWithdrawal(TransactionWasPerform $event): void
    {
        $type = $event->type();
        Assertion::eq(Transaction::TYPES_ROLLBACK_WITHDRAWAL, $type, "Transaction must be a withdrawal type, $type given");
        Assertion::true($this->owner()->eq($event->userId()), 'This transaction is not for that user');

        $this->amount = $this->amount->add($event->amount());
    }


    protected function aggregateId(): string
    {
        return $this->owner()->toString();
    }

    /**
     * @param AggregateChanged|BalanceWasCreated $event
     */
    protected function apply(AggregateChanged $event): void
    {
        $eventClass = get_class($event);

        switch (true) {
            case $event instanceof BalanceWasCreated:

                $this->whenCreated($event);
                break;

            case $event instanceOf TransactionWasPerform:

                switch ($event->type()) {
                    case Transaction::TYPES_DEPOSIT:
                        $this->whenDeposit($event);
                        break;
                    case Transaction::TYPES_WITHDRAWAL:
                        $this->whenWithdrawal($event);
                        break;
                    case Transaction::TYPES_ROLLBACK_DEPOSIT:
                        $this->whenRollbackDeposit($event);
                        break;
                    case Transaction::TYPES_ROLLBACK_WITHDRAWAL:
                        $this->whenRollbackWithdrawal($event);
                        break;
                }
                break;

            default:
                throw new \RuntimeException("No handler found for $eventClass");
        }
    }

    public static function fromHistory(\Iterator $events)
    {
        $balance = new self();

        $balance->replay($events);

        return $balance;
    }

    public function popRecordedEvents(): array
    {
        return parent::popRecordedEvents();
    }
}
