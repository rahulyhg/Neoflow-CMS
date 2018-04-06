<?php
namespace Neoflow\Framework\Persistence\Querying;

use Neoflow\Framework\Persistence\Statement;

/**
 * @method DeleteQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method DeleteQuery whereRaw(string $condition, array $parameters)               Add raw WHERE condition
 */
class DeleteQuery extends AbstractQuery
{

    /**
     * WHERE query trait.
     */
    use WhereTrait;

    /**
     * @var array
     */
    protected $clauses = [
        'DELETE FROM' => false,
        'FROM' => null,
        'WHERE' => ' AND ',
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

        $this->addStatement('DELETE FROM', $this->quoteIdentifier($table));
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
     * Enable/disable ingoring and query fails silently.
     *
     * @return self
     */
    public function ignore(bool $enable = true): self
    {
        $this->ignore = $enable;

        return $this;
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
            return str_replace('DELETE', 'DELETE IGNORE', $query);
        }

        return $query;
    }

    /**
     * Execute delete query.
     *
     * @param int $id
     *
     * @return Statement
     */
    public function execute(int $id = 0): Statement
    {
        if (0 !== $id) {
            $this->where($this->primaryKey, '=', $id);
        }

        $statement = $this->executeQuery();
        if (!$this->preventCacheClearing) {
            $this->cache()->deleteByTag('database-results');
        }

        return $statement;
    }
}
