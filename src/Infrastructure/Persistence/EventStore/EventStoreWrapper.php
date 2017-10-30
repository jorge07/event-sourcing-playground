<?php

namespace Leos\Infrastructure\Persistence\EventStore;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

use Leos\Infrastructure\Persistence\EventStore\Schema\MySQLEventStoreSchema;

use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy;
use Prooph\EventStore\StreamName;

class EventStoreWrapper
{
    /**
     * @var MysqlEventStoreConfig
     */
    private $config;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var \Prooph\EventStore\Pdo\MySqlEventStore
     */
    private $eventStore;
    /**
     * @var string
     */
    private $eventStreamsTable;

    /**
     * @var \PDO|Connection
     */
    private $connection;

    /**
     * @var MySqlSingleStreamStrategy
     */
    private $strategy;

    private $defaultStream = 'event_stream';

    /**
     * @var MySQLEventStoreSchema
     */
    private $eventStoreSchema;

    /**
     * @var ActionEventEmitterEventStore
     */
    private $actionEventEmitterStore;
    
    public function __construct(MysqlEventStoreConfig $config, string $eventStreamsTable = 'event_streams')
    {
        $this->config = $config;
        $this->configuration = new Configuration();
        $this->eventStreamsTable = $eventStreamsTable;
        $this->eventStoreSchema = new MySQLEventStoreSchema($eventStreamsTable);
        $this->connection = DriverManager::getConnection(
            $this->config->dbalConfig(),
            $this->configuration
        )->getWrappedConnection();
        
        $this->strategy = new MySqlSingleStreamStrategy();
        
        $this->eventStore = new MySqlEventStore(
            new FQCNMessageFactory(),
            $this->connection,
            $this->strategy,
            100,
            $this->eventStreamsTable,
            false
        );
        
        $this->actionEventEmitterStore = $this->createActionEventEmitterEventStore($this->eventStore);
    }

    public function createSchema(): void
    {
        $statement = $this->connection->prepare(
            $this->eventStoreSchema->schema()
            . $this->strategy->createSchema(
                $this->strategy->generateTableName(
                    new StreamName($this->defaultStream)
                )
            )[0]
        );

        $statement->execute();
    }

    public function deleteSchema(): void
    {
        $statement = $this->connection->prepare(
            "DROP TABLE IF EXISTS $this->eventStreamsTable; DROP TABLE IF EXISTS $this->eventStreamsTable"
        );

        $statement->execute();
    }

    public function resetEventStreams(): void
    {
        $statement = $this->connection->prepare("TRUNCATE TABLE $this->eventStreamsTable");

        $statement->execute();
    }

    public function resetEvents(): void
    {
        $tableName = $this->strategy->generateTableName(new StreamName($this->defaultStream));

        $statement = $this->connection->prepare("TRUNCATE TABLE $tableName");

        $statement->execute();
    }

    public function eventStore(): MySqlEventStore
    {
        return $this->eventStore;
    }

    protected function createActionEventEmitterEventStore(EventStore $eventStore): ActionEventEmitterEventStore
    {
        return new ActionEventEmitterEventStore(
            $eventStore,
            new ProophActionEventEmitter([
                ActionEventEmitterEventStore::EVENT_APPEND_TO,
                ActionEventEmitterEventStore::EVENT_CREATE,
                ActionEventEmitterEventStore::EVENT_LOAD,
                ActionEventEmitterEventStore::EVENT_LOAD_REVERSE,
                ActionEventEmitterEventStore::EVENT_DELETE,
                ActionEventEmitterEventStore::EVENT_HAS_STREAM,
                ActionEventEmitterEventStore::EVENT_FETCH_STREAM_METADATA,
                ActionEventEmitterEventStore::EVENT_UPDATE_STREAM_METADATA,
                ActionEventEmitterEventStore::EVENT_FETCH_STREAM_NAMES,
                ActionEventEmitterEventStore::EVENT_FETCH_STREAM_NAMES_REGEX,
                ActionEventEmitterEventStore::EVENT_FETCH_CATEGORY_NAMES,
                ActionEventEmitterEventStore::EVENT_FETCH_CATEGORY_NAMES_REGEX,
            ])
        );
    }

    public function actionEventEmitterStore(): ActionEventEmitterEventStore
    {
        return $this->actionEventEmitterStore;
    }

}
