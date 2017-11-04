<?php

namespace Leos\Infrastructure\User\Persistence;

use Leos\Domain\Shared\Exception\NotFoundException;
use Leos\Domain\User\Aggregate\User;
use Leos\Domain\User\Repository\UserRepositoryInterface;
use Leos\Domain\User\ValueObject\UserId;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\StreamName;

final class UserRepository extends AggregateRepository implements UserRepositoryInterface
{
    public function __construct(ActionEventEmitterEventStore $eventStoreWrapper)
    {
        parent::__construct(
            $eventStoreWrapper,
            AggregateType::fromAggregateRootClass(User::class),
            new AggregateTranslator(),
            null,
            new StreamName('event_stream'),
            false
        );
    }

    public function get(UserId $uuid): User
    {
        /** @var User|null $user */
        $user = $this->getAggregateRoot($uuid->toString());

        if (null === $user) {

            throw new NotFoundException("User not found");
        }

        return $user;
    }

    public function store(User $user): void
    {
        $this->saveAggregateRoot($user);
    }
}
