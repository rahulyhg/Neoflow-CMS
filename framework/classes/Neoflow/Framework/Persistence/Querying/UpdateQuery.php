<?php

namespace Neoflow\Framework\Persistence\Querying;

use Neoflow\Framework\Persistence\Statement;
use RuntimeException;

/**
 * @method UpdateQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method UpdateQuery whereRaw(string $condition, array $parameters)               Add raw WHERE condition
 */
class UpdateQuery extends AbstractQuery
{
    /**
     * Where query trait.
     */
    use WhereTrait;

    /**
     * @var array
     */
    protected $clauses = [
        'UPDATE' => false,
        'SET' => ', ',
        'WHERE' => ' AND ',
    ];

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

        $this->addStatement('UPDATE', $this->quoteIdentifier($table));
    }

    /**
     * Prevent cache clearing.
     *
     * @param bool $enable
     *
     * @return self
     */
    public function preventCacheClearing(bool $enable = false): self
    {
        $this->preventCacheClearing = $enable;

        return $this;
    }

    /**
     * Add values to UPDATE statement.
     *
     * @param array $set
     *
     * @return UpdateQuery
     */
    public function set(array $set = []): self
    {
        foreach ($set as $column => $value) {
            $this->statements['SET'][] = $this->quoteIdentifier($column).' = ?';
            $this->parameters['SET'][] = $value;
        }

        return $this;
    }

    /**
     * Execute update query.
     *
     * @param int $id
     *
     * @return Statement
     */
    public function execute(int $id = 0): Statement
    {
        if (count($this->statements['SET']) > 0) {
            if ($id) {
                $this->where($this->primaryKey, '=', $id);
            }

            $statement = $this->executeQuery();

            if (!$this->preventCacheClearing) {
                $this->cache()->deleteByTag('db_results');
            }

            return $statement;
        }

        throw new RuntimeException('There is no SET for query definied');
    }
}
