<?php

namespace Leos\UI\CLI\Command;

final class EventStoreSchemaDeleteCommand extends EventStoreSchemaCreateCommand
{
    protected function configure()
    {
        $this
            ->setName('leos:event_store:schema_delete')
            ->setDescription('Delete Event Store Schema')
        ;
    }

    protected function apply(): void
    {
        $this->eventStoreFactory->deleteSchema();
    }
}
