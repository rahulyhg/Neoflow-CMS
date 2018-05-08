<?php

namespace Neoflow\Framework\Persistence\Querying;

use Neoflow\Framework\Persistence\Statement;

/**
 * @method InsertQuery where(string $condition, array $parameters) Add WHERE condition
 */
class InsertQuery extends AbstractQuery
{
    /**
     * @var array
     */
    protected $clauses = [
        'INSERT INTO' => false,
        'VALUES' => ', ',
    ];

    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * @var bool
     */
    protected $preventCacheClearing = false;

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        parent::__construct($table);

        $this->addStatement('INSERT INTO', $this->quoteIdentifier($table));

        $this->clauses['VALUES'] = function ($clauseStatement) {
            $query = ' ('.implode(', ', $clauseStatement).') VALUES ('.str_repeat('?, ', count($clauseStatement) - 1).'?) ';

            return $query;
        };
    }

    /**
     * Prevent cache clearing.
     *
     * @param bool $prevent
     *
     * @return self
     */
    public function preventCacheClearing(bool $prevent = false): self
    {
        $this->preventCacheClearing = $prevent;

        return $this;
    }

    /**
     * Execute insert query.
     *
     * @return Statement
     */
    public function execute(): Statement
    {
        $statement = $this->executeQuery();
        if (!$this->preventCacheClearing) {
            $this->cache()->deleteByTag('db_results');
        }

        return $statement;
    }

    /**
     * Build query with statements.
     *
     * @return string
     */
    protected function buildQuery(): string
    {
        $query = parent::buildQuery();
        if ($this->ignore) {
            return str_replace('INSERT INTO', 'INSERT IGNORE INTO', $query);
        }

        return $query;
    }

    /**
     * Add values to INSERT statement.
     *
     * @param array $values
     *
     * @return self
     */
    public function values(array $values = []): self
    {
        foreach ($values as $column => $value) {
            $this->statements['VALUES'][] = $this->quoteIdentifier($column);
            $this->parameters['VALUES'][] = $value;
        }

        return $this;
    }

    /**
     * Enable/disable ingoring and query fails silently.
     *
     * @return self
     */
    public function ignore(bool $enable = true): self
    {
        $this->ignore = $enable;

        return $this;
    }
}
