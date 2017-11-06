<?php

namespace Leos\UI\CLI\Command;

use Leos\Infrastructure\Shared\Persistence\EventStore\EventStoreFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventStoreSchemaCreateCommand extends Command
{
    /**
     * @var EventStoreFactory
     */
    protected $eventStoreFactory;

    public function __construct(EventStoreFactory $eventStoreFactory)
    {
        parent::__construct();

        $this->eventStoreFactory = $eventStoreFactory;
    }

    protected function configure()
    {
        $this
            ->setName('leos:event_store:schema_create')
            ->setDescription('Create Event Store Schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $this->apply();

        } catch (\Exception $exception) {

            $output->writeln('<error>Impossible to perform schema operation</error>');
            $output->writeln('<error>Reason:</error>');
            $output->writeln('   ' . $exception->getMessage());

            throw $exception;
        }

        $output->writeln('<info>Done.</info>');
    }

    protected function apply(): void
    {
        $this->eventStoreFactory->createSchema();
    }

}
