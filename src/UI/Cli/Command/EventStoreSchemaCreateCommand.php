<?php

namespace Leos\UI\Cli\Command;

use Leos\Infrastructure\Persistence\EventStore\EventStoreWrapper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventStoreSchemaCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('leos:event_store:schema_create')
            ->setDescription('Create Event Store Schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EventStoreWrapper $eventStore */
        $eventStore = $this->getContainer()->get('event_store');

        try {

            $this->apply($eventStore);

        } catch (\Exception $exception) {

            $output->writeln('<error>Impossible to create schema</error>');
            $output->writeln('<error>Reason:</error>');
            $output->writeln('   ' . $exception->getMessage());

            throw $exception;
        }

        $output->writeln('<info>Done.</info>');
    }

    protected function apply(EventStoreWrapper $eventStore): void
    {
        $eventStore->createSchema();
    }

}