<?php

namespace Leos\UI\Cli\Command;

use Leos\Infrastructure\Persistence\EventStore\EventStoreWrapper;

class EventStoreSchemaDeleteCommand extends EventStoreSchemaCreateCommand
{
    protected function configure()
    {
        $this
            ->setName('leos:event_store:schema_delete')
            ->setDescription('Delete Event Store Schema')
        ;
    }

    protected function apply(EventStoreWrapper $eventStore): void
    {
        $eventStore->deleteSchema();
    }
}
