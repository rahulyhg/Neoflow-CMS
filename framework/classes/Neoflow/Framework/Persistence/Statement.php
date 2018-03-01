<?php

namespace Neoflow\Framework\Persistence;

use Neoflow\Framework\AppTrait;
use PDOStatement;

class Statement extends PDOStatement
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Query counter trait.
     */
    use QueryCounterTrait;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var type
     */
    protected $parameters = [];

    /**
     * Constructor.
     *
     * @param Database $database
     */
    protected function __construct(Database $database)
    {
        $this->database = $database;

        $this->logger()->debug('Database statement created');
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $parameters
     *
     * @return bool
     */
    public function execute($parameters = null): bool
    {
        $result = parent::execute($parameters);

        $this->logger()
                ->debug('Database statement executed', [
                    'Query' => $this->queryString,
                    'Parameters' => count($parameters) ? $parameters : 'Bind by reference and not logged',
                    'Result' => $this->rowCount().' rows affected',
        ]);

        $this->countUpExecutedQueries();

        return $result;
    }
}
