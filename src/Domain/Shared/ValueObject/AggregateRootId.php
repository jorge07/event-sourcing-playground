<?php

namespace Leos\Domain\Shared\ValueObject;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class AggregateRootId
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public static function new()
    {
        return new static(Uuid::uuid4());
    }

    public static function fromString(string $userId)
    {
        return new static(Uuid::fromString($userId));
    }

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function eq(string $aggregateRootId): bool
    {
        return $this->uuid->equals(Uuid::fromString($aggregateRootId));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }
}
