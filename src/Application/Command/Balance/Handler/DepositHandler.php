<?php

namespace Leos\Application\Command\Balance\Handler;

use Leos\Application\Command\Balance\DepositCommand;
use Leos\Domain\Balance\Aggregate\Balance;
use Leos\Domain\Balance\Repository\BalanceRepositoryInterface;
use Leos\Domain\Balance\ValueObject\BalanceOwnerId;

class DepositHandler
{
    /**
     * @var BalanceRepositoryInterface
     */
    private $balanceRepository;

    public function __construct(BalanceRepositoryInterface $balanceRepository)
    {
        $this->balanceRepository = $balanceRepository;
    }

    public function handle(DepositCommand $request): void
    {
        $balance = $this->userBalance($request->userId());

        $balance->deposit($request->amount());

        $this->balanceRepository->store($balance);
    }

    private function userBalance(string $userId): Balance
    {
        $uuid = BalanceOwnerId::fromString($userId);

        return $this->balanceRepository->oneFromOwner($uuid);
    }
}
