<?php

namespace Neoflow\Framework\Persistence;

use Exception;
use Neoflow\Framework\AppTrait;
use PDO;

class Database extends PDO
{
    /**
     * Traits.
     */
    use AppTrait;
    use QueryCounterTrait;

    /**
     * Constructor.
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array  $options
     */
    public function __construct($dsn, $username = null, $password = null, array $options = null)
    {
        parent::__construct($dsn, $username, $password, $options);

        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['\\Neoflow\\Framework\\Persistence\\Statement', [$this]]);

        $databaseConnections = $this->app()->get('databaseConnections');
        $this->app()->set('databaseConnections', $databaseConnections++);

        $this->logger()->info('Database connection established', [
            'DSN' => $dsn,
            'Username' => $username,
            'Password' => '*****',
            'Options' => $options,
        ]);
    }

    /**
     * Execute an SQL query and return the number of affected rows.
     *
     * @param string $query SQL query
     *
     * @return int
     */
    public function exec($query)
    {
        $result = parent::exec($query);

        $this->logger()->debug('Database query executed', [
            'Query' => $query,
            'Result' => $result.' rows affected',
        ]);

        $this->countUpExecutedQueries();

        return $result;
    }

    /**
     * Execute an SQL file and return the number of affected rows.
     *
     * @param string $file SQL file
     *
     * @return int
     */
    public function executeFile(string $file): int
    {
        if (is_file($file)) {
            $query = trim(file_get_contents($file));
            if ($query) {
                return $this->exec($query);
            }
        }

        return 0;
    }

    /**
     * Check whether table exist (without exception).
     *
     * @param string $table
     *
     * @return bool
     */
    public function hasTable(string $table)
    {
        try {
            $result = $this->query('SELECT 1 FROM `'.$this->quote($table).'` LIMIT 1');
        } catch (Exception $e) {
            return false;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return false !== $result;
    }

    /**
     * Etablish and create database connection.
     *
     * @param string $host
     * @param string $dbname
     * @param string $username
     * @param string $password
     * @param string $charset
     * @param array  $options
     *
     * @return self
     */
    public static function connect(string $host, string $dbname, string $username = '', string $password = '', string $charset = 'UTF8', array $options = []): self
    {
        // Define DSN string
        $dsn = 'mysql:host='.$host.';dbname='.$dbname.';charset='.$charset;

        // Create database connection
        return new self($dsn, $username, $password, $options + [
            self::ATTR_PERSISTENT => false,
            self::ATTR_ERRMODE => self::ERRMODE_EXCEPTION,
            self::ATTR_STRINGIFY_FETCHES => false,
        ]);
    }
}
