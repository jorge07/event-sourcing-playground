<?php

namespace Leos\Infrastructure\Shared\Persistence\EventStore\Schema;

final class MySQLEventStoreSchema
{
    /**
     * @var string
     */
    private $tableName;

    public function __construct(string $tableName = 'event_stream')
    {
        $this->tableName = $tableName;
    }

    public function schema(): string
    {
        $statement = <<<EOT
CREATE TABLE $this->tableName (
  `no` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `real_stream_name` VARCHAR(150) NOT NULL,
  `stream_name` CHAR(41) NOT NULL,
  `metadata` JSON,
  `category` VARCHAR(150),
  PRIMARY KEY (`no`),
  UNIQUE KEY `ix_rsn` (`real_stream_name`),
  KEY `ix_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOT;

        return $statement;
    }
}
