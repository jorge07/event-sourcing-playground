<?php

namespace Leos\Domain\Balance\Model;

use Assert\Assertion;
use Leos\Domain\Balance\ValueObject\TransactionId;
use Money\Money;

class Transaction
{
    const TYPES_DEPOSIT = 'deposit';
    const TYPES_WITHDRAWAL = 'withdrawal';
    const TYPES_ROLLBACK_DEPOSIT = 'rollback_deposit';
    const TYPES_ROLLBACK_WITHDRAWAL = 'rollback_withdrawal';

    const VALID_TYPES = [
        self::TYPES_DEPOSIT,
        self::TYPES_WITHDRAWAL,
        self::TYPES_ROLLBACK_DEPOSIT,
        self::TYPES_ROLLBACK_WITHDRAWAL,
    ];

    const ROLLBACK_MAP = [
        self::TYPES_DEPOSIT => self::TYPES_ROLLBACK_DEPOSIT,
        self::TYPES_WITHDRAWAL => self::TYPES_ROLLBACK_WITHDRAWAL,
    ];

    /**
     * @var TransactionId
     */
    private $uuid;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Money
     */
    private $amount;

    protected function __construct(string $type, Money $amount)
    {
        $this->uuid = TransactionId::new();
        $this->setType($type);
        $this->amount = $amount;
    }

    public static function deposit(Money $money): self
    {
        return new self(Transaction::TYPES_DEPOSIT, $money);
    }

    public static function withdrawal(Money $money): self
    {
        return new self(Transaction::TYPES_WITHDRAWAL, $money);
    }

    public function rollback(): self
    {
        $transactionType = $this->type;

        Assertion::true(Transaction::canRollback($transactionType), "Cannot rollback $transactionType");

        return new self(Transaction::ROLLBACK_MAP[$transactionType], $this->amount);
    }

    private static function canRollback(string $type): bool
    {
        return array_key_exists($type, self::ROLLBACK_MAP);
    }

    private function setType(string $type): void
    {
        Assertion::inArray($type, self::VALID_TYPES);

        $this->type = $type;
    }

    public function uuid(): TransactionId
    {
        return $this->uuid;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function amount(): Money
    {
        return $this->amount;
    }
}
