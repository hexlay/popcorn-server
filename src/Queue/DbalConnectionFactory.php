<?php

namespace App\Queue;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Tools\DsnParser;
use Enqueue\Dbal\DbalContext;
use Interop\Queue\ConnectionFactory;
use Interop\Queue\Context;

class DbalConnectionFactory implements ConnectionFactory
{
    private array $config;
    private ?Connection $connection = null;

    public function __construct(array|string|null $config = [])
    {
        $this->config = array_replace_recursive([
            'connection' => [],
            'table_name' => 'enqueue',
            'polling_interval' => 1000,
            'lazy' => true,
        ], $this->normalizeConfig($config));
    }

    public function createContext(): Context
    {
        if ($this->config['lazy']) {
            return new DbalContext(fn () => $this->establishConnection(), $this->config);
        }

        return new DbalContext($this->establishConnection(), $this->config);
    }

    public function close(): void
    {
        if ($this->connection instanceof Connection) {
            $this->connection->close();
        }
    }

    private function establishConnection(): Connection
    {
        if (!$this->connection instanceof Connection) {
            $configuration = new Configuration();
            $configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

            $this->connection = DriverManager::getConnection($this->config['connection'], $configuration);
        }

        return $this->connection;
    }

    private function normalizeConfig(array|string|null $config): array
    {
        if (empty($config)) {
            return [];
        }

        if (is_string($config)) {
            return [
                'connection' => $this->parseDsn($config),
            ];
        }

        if (isset($config['dsn']) && !isset($config['connection'])) {
            $config['connection'] = $this->parseDsn($config['dsn']);
        }

        unset($config['dsn']);

        return $config;
    }

    private function parseDsn(string $dsn): array
    {
        return (new DsnParser([
            'mysql' => 'pdo_mysql',
            'mysql2' => 'pdo_mysql',
            'mysql+pdo' => 'pdo_mysql',
            'postgres' => 'pdo_pgsql',
            'postgresql' => 'pdo_pgsql',
            'pgsql' => 'pdo_pgsql',
            'pgsql+pdo' => 'pdo_pgsql',
            'sqlite' => 'pdo_sqlite',
            'sqlite3' => 'pdo_sqlite',
            'sqlite+pdo' => 'pdo_sqlite',
        ]))->parse($dsn);
    }
}
