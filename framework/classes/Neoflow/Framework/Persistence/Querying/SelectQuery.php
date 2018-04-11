<?php
namespace Neoflow\Framework\Persistence\Querying;

use Neoflow\Framework\Common\Collection;
use Neoflow\Framework\Persistence\QueryCounterTrait;
use Neoflow\Framework\Persistence\Statement;

/**
 * @method SelectQuery where(string $condition, string $operator, mixed $parameter) Add WHERE condition
 * @method SelectQuery whereRaw(string $condition, array $parameters)               Add raw WHERE condition
 * @method SelectQuery leftJoin(string $table, array $condition)                    Add LEFT JOIN statement
 * @method SelectQuery innerJoin(string $table, array $condition)                   Add INNER JOIN statement
 * @method SelectQuery rightJoin (string $table, array $condition)                  Add INNER JOIN statement
 * @method SelectQuery outerJoin(string $table, array $condition)                   Add INNER JOIN statement
 */
class SelectQuery extends AbstractQuery
{

    /**
     * Query traits.
     */
    use WhereTrait;
    use JoinTrait;
    use QueryCounterTrait;

    /**
     * @var array
     */
    protected $clauses = [
        'SELECT' => ', ',
        'FROM' => false,
        'LEFT JOIN' => false,
        'INNER JOIN' => false,
        'RIGHT JOIN' => false,
        'OUTER JOIN' => false,
        'WHERE' => ' AND ',
        'GROUP BY' => ', ',
        'HAVING' => ' AND ',
        'ORDER BY' => ', ',
        'LIMIT' => false,
        'OFFSET' => false,
    ];

    /**
     * @var bool
     */
    protected $caching = false;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * Constructor.
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        parent::__construct($table);

        $this->addStatement('FROM', $this->quoteIdentifier($table));

        $this->caching = (bool) $this->config()->get('cache')->get('for_qb');
    }

    /**
     * Fetch only one column.
     *
     * @param string $column
     *
     * @return mixed
     */
    public function fetchColumn(string $column = '')
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey('fetchColumn');

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if (null === $result) {
            // Fetch from database
            $statement = $this->asObject(false)->execute();
            if ($statement) {
                $result = $statement->fetch();
                if ($result) {
                    $result = $result[$column];
                }
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Fetch only first column.
     *
     * @return mixed
     */
    public function fetchFirstColumn()
    {
        return $this->fetchColumnByIndex(0);
    }

    /**
     * Fetch only one column by index.
     *
     * @param int $columnIndex
     *
     * @return mixed
     */
    public function fetchColumnByIndex(int $columnIndex)
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey('fetchColumnByIndex');

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if (null === $result) {
            // Fetch from database
            $statement = $this->asObject(false)->execute();
            if ($statement) {
                $result = $statement->fetchColumn($columnIndex);
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Execute select query.
     *
     * @return Statement
     */
    public function execute(): Statement
    {
        if (0 === count($this->statements['SELECT'])) {
            $this->statements['SELECT'][] = '*';
        }

        return $this->executeQuery();
    }

    /**
     * Add SELECT for column(s).
     *
     * @param string $column
     *
     * @return self
     */
    public function select(string $column): self
    {
        return $this->addStatement('SELECT', $this->quoteIdentifier($column));
    }

    /**
     * Add raw SELECT statement.
     *
     * @param string $statement
     *
     * @return self
     */
    public function selectRaw(string $statement): self
    {
        return $this->addStatement('SELECT', $statement);
    }

    /**
     * Add GROUP BY for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function groupBy(string $column): self
    {
        return $this->addStatement('GROUP BY', $this->quoteIdentifier($column));
    }

    /**
     * Add HAVING for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function having(string $column): self
    {
        return $this->addStatement('HAVING', $this->quoteIdentifier($column));
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByAsc(string $column): self
    {
        return $this->orderByRaw($this->quoteIdentifier($column) . ' ASC');
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByDesc(string $column): self
    {
        return $this->orderByRaw($this->quoteIdentifier($column) . ' DESC');
    }

    /**
     * Add raw ORDER BY statement.
     *
     * @param string $statement
     *
     * @return self
     */
    public function orderByRaw(string $statement): self
    {
        return $this->addStatement('ORDER BY', $statement);
    }

    /**
     * Add LIMIT.
     *
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self
    {
        return $this->addStatement('LIMIT', $limit);
    }

    /**
     * Add OFFSET.
     *
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self
    {
        return $this->addStatement('OFFSET', $offset);
    }

    /**
     * Fetch first row.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function fetch(int $id = 0)
    {
        if ($id) {
            $this->where($this->primaryKey, '=', $id);
        }

        $this->limit(1);

        // Generate cache key
        $cacheKey = $this->generateCacheKey();

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if (null === $result) {
            // Fetch from database
            $statement = $this->execute();
            if ($statement) {
                $result = $statement->fetch();
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Fetch all rows.
     *
     * @return Collection
     */
    public function fetchAll(): Collection
    {
        // Generate cache key
        $cacheKey = $this->generateCacheKey();

        // Fetch from cache
        $result = $this->fetchFromCache($cacheKey);
        if (null === $result) {
            // Fetch from database
            $statement = $this->execute();
            if ($statement) {
                $result = $statement->fetchAll();
            }

            // Store to cache
            $this->storeToCache($cacheKey, $result);
        }

        if (is_array($result)) {
            return new Collection($result);
        }

        return new Collection();
    }

    /**
     * Count rows.
     *
     * @return int
     */
    public function count(): int
    {
        return (int) $this->selectRaw('COUNT(*)')->fetchFirstColumn();
    }

    /**
     * Set fetch mode as object.
     *
     * @param bool|string $enable
     *
     * @return self
     */
    public function asObject($enable = true): self
    {
        $this->asObject = $enable;

        return $this;
    }

    /**
     * Enable/disable caching.
     *
     * @param bool $enable
     *
     * @return self
     */
    public function caching(bool $enable = true): self
    {
        $this->caching = $enable;

        return $this;
    }

    /**
     * Store result to cache.
     *
     * @param string $cacheKey
     * @param mixed  $result
     *
     * @return bool
     */
    protected function storeToCache(string $cacheKey, $result): bool
    {
        if ($this->caching) {
            return $this->cache()->store($cacheKey, $result, 0, ['database-results']);
        }

        return false;
    }

    /**
     * Fetch result from cache.
     *
     * @param string $cacheKey
     *
     * @return mixed
     */
    protected function fetchFromCache(string $cacheKey)
    {
        if ($this->caching) {
            if ($this->cache()->exists($cacheKey)) {
                $result = $this->cache()->fetch($cacheKey);
                $this->countUpCachedQueries();

                return $result;
            }
        }

        return null;
    }

    /**
     * Generate cache key.
     *
     * @param string $salt
     *
     * @return string
     */
    protected function generateCacheKey(string $salt = ''): string
    {
        if (!$this->cacheKey) {
            return sha1($salt . $this->getQuery() . ':' . implode('|', array_map(function ($parameter) {
                        if (is_array($parameter)) {
                            return implode('|', $parameter);
                        }

                        return $parameter;
                    }, $this->getParameters())));
        }

        return $this->cacheKey;
    }
}
