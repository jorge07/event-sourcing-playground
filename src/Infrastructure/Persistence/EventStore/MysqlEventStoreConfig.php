<?php

namespace Leos\Infrastructure\Persistence\EventStore;

class MysqlEventStoreConfig
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string
     */
    private $streamsTable;

    public function __construct(string $host, string $user, string $pass, string $database, string $streamsTable)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
        $this->streamsTable = $streamsTable;
    }

    public function dbalConfig(): array
    {
        return [
            'dbname' => $this->database,
            'user' => $this->user,
            'password' => $this->pass,
            'host' => $this->host,
            'driver' => 'pdo_mysql',
        ];
    }

    public function streamsTable(): string
    {
        return $this->streamsTable;
    }
}
