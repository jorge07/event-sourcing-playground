<?php

namespace Leos\Infrastructure\User\Persistence;

use Leos\Domain\Shared\Exception\NotFoundException;
use Leos\Domain\User\Aggregate\User;
use Leos\Domain\User\Repository\UserRepositoryInterface;
use Leos\Domain\User\ValueObject\UserId;
use Leos\Infrastructure\Persistence\EventStore\EventStoreWrapper;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\StreamName;

class UserRepository extends AggregateRepository implements UserRepositoryInterface
{
    public function __construct(EventStoreWrapper $eventStoreWrapper)
    {
        parent::__construct(
            $eventStoreWrapper->eventStore(),
            AggregateType::fromAggregateRootClass(User::class),
            new AggregateTranslator(),
            null, //We don't use a snapshot store in the example
            new StreamName('event_stream'),
            false //But we enable the "one-stream-per-aggregate" mode
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
