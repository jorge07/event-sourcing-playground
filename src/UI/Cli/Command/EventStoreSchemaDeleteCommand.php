<?php

namespace Leos\UI\Cli\Command;

use Leos\Infrastructure\Persistence\EventStore\EventStoreFactory;

final class EventStoreSchemaDeleteCommand extends EventStoreSchemaCreateCommand
{
    protected function configure()
    {
        $this
            ->setName('leos:event_store:schema_delete')
            ->setDescription('Delete Event Store Schema')
        ;
    }

    protected function apply(EventStoreFactory $eventStore): void
    {
        $eventStore->deleteSchema();
    }
}
