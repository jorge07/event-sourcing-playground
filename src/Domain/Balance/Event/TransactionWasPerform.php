<?php

namespace Leos\Domain\Balance\Event;

use Leos\Domain\Balance\Model\Transaction;
use Leos\Domain\Balance\ValueObject\TransactionId;
use Money\Money;
use Prooph\EventSourcing\AggregateChanged;

final class TransactionWasPerform extends AggregateChanged
{
    public static function deposit(string $uuid, TransactionId $transactionId, Money $amount)
    {
        return self::occur($uuid, [
            'transactionId' => $transactionId->toString(),
            'type' => Transaction::TYPES_DEPOSIT,
            'userId' => $uuid,
            'amount' => $amount
        ]);
    }

    public static function withdrawal(string $uuid, TransactionId $transactionId, Money $amount)
    {
        return self::occur($uuid, [
            'transactionId' => $transactionId->toString(),
            'type' => Transaction::TYPES_WITHDRAWAL,
            'userId' => $uuid,
            'amount' => $amount
        ]);
    }

    public static function rollback(string $uuid, TransactionId $transactionId, TransactionId $transactionToRollback, string $transactionType, Money $amount)
    {
        return self::occur($uuid, [
            'transactionId' => $transactionId,
            'type' => $transactionType,
            'transactionToRollbackId' => $transactionToRollback->toString(),
            'userId' => $uuid,
            'amount' => $amount
        ]);
    }

    public function type(): string
    {
        return $this->payload['type'];
    }

    public function userId(): string
    {
        return $this->payload['userId'];
    }

    public function amount(): Money
    {
        return $this->payload['amount'];
    }
}
