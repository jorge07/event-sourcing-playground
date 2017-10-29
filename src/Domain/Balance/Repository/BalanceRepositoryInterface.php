<?php

namespace Leos\Domain\Balance\Repository;

use Leos\Domain\Balance\Aggregate\Balance;
use Leos\Domain\Balance\ValueObject\BalanceOwnerId;

interface BalanceRepositoryInterface
{
    public function oneFromOwner(BalanceOwnerId $balanceId): Balance;

    public function store(Balance $balance): void;
}
