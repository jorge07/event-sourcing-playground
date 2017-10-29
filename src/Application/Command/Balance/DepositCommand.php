<?php

namespace Leos\Application\Command\Balance;

use Money\Currency;
use Money\Money;

class DepositCommand
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var Money
     */
    private $amount;

    public function __construct(string $userId, string $amount, string $currency)
    {
        $this->userId = $userId;
        $this->amount = new Money(
            $amount,
            new Currency(
                $currency
            )
        );
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

}
