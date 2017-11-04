<?php

namespace Leos\Domain\Balance\Event;

use Money\Money;
use Prooph\EventSourcing\AggregateChanged;

final class BalanceWasCreated extends AggregateChanged
{
    public static function new(string $uuid, Money $money): self
    {
        /** @var self $event */
        $event = self::occur($uuid, [
            'userId' => $uuid,
            'amount' => $money
        ]);

        return $event;
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
