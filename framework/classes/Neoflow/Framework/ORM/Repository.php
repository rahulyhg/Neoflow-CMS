<?php

namespace Neoflow\Framework\ORM;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Common\Collection;
use Neoflow\Framework\Core\AbstractModel;
use Neoflow\Framework\Persistence\QueryBuilder;
use Neoflow\Framework\Persistence\Querying\DeleteQuery;
use Neoflow\Framework\Persistence\Querying\InsertQuery;
use Neoflow\Framework\Persistence\Querying\SelectQuery;
use Neoflow\Framework\Persistence\Querying\UpdateQuery;
use RuntimeException;

class Repository
{
    /**
     * Load app.
     */
    use AppTrait;

    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var QueryBuilder|SelectQuery|DeleteQuery|UpdateQuery|InsertQuery
     */
    protected $query;

    /**
     * Get query of query builder.
     *
     * @return SelectQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set repository for model entity.
     *
     * @param string $modelClassName
     * @param bool   $asSelect
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function forModel($modelClassName, $asSelect = true)
    {
        $this->reset();

        if (class_exists($modelClassName)) {
            $this->modelClassName = $modelClassName;

            $this->query = new QueryBuilder();

            if ($asSelect) {
                $this->query = $this->query
                    ->selectFrom($this->getTableName())
                    ->setPrimaryKey($this->getPrimaryKey())
                    ->asObject($modelClassName);

                $caching = $this->config()->get('cache')->get('for_orm');
                $this->caching($caching);
            }

            return $this;
        }
        throw new RuntimeException('Model class "'.$modelClassName.'" not found');
    }

    /**
     * Delete model entity.
     *
     * @param AbstractModel $entity
     *
     * @return bool
     */
    public function delete(AbstractModel $entity): bool
    {
        if ($entity->id()) {
            $this->forModel(get_class($entity), false);
            $result = $this->query
                ->deleteFrom($this->getTableName())
                ->setPrimaryKey($this->getPrimaryKey())
                ->execute($entity->id());

            if ($result) {
                $this->logger()->debug('Model entity deleted', [
                    'Type' => $entity->getReflection()->getName(),
                    'ID' => $entity->id(),
                ]);
            }

            return (bool) $result;
        }

        return true;
    }

    /**
     * Update model entity.
     *
     * @param AbstractModel $entity
     * @param bool          $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return \Neoflow\Framework\Persistence\Statement
     */
    public function update(AbstractModel $entity, bool $preventCacheClearing = false): \Neoflow\Framework\Persistence\Statement
    {
        $this->forModel(get_class($entity), false);

        $statement = $this->query
            ->update($this->getTableName())
            ->setPrimaryKey($this->getPrimaryKey())
            ->preventCacheClearing($preventCacheClearing)
            ->set($entity->getModifiedData())
            ->execute($entity->id());

        if ($statement) {
            $this->logger()->debug('Model entity saved', [
                'Type' => $entity->getReflection()->getName(),
                'ID' => $entity->id(),
            ]);
        }

        return $statement;
    }

    /**
     * Insert model entity.
     *
     * @param AbstractModel $entity
     * @param bool          $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return \Neoflow\Framework\Persistence\Statement
     */
    public function insert(AbstractModel $entity, bool $preventCacheClearing = false): \Neoflow\Framework\Persistence\Statement
    {
        $this->forModel(get_class($entity), false);

        $statement = $this->query
            ->insertInto($this->getTableName())
            ->preventCacheClearing($preventCacheClearing)
            ->values($entity->getData())
            ->execute();

        if ($statement) {
            $this->logger()->debug('New model entity saved', [
                'Type' => $entity->getReflection()->getName(),
                'ID' => $this->database()->lastInsertId(),
            ]);
        }

        return $statement;
    }

    /**
     * Save model entity.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return \Neoflow\Framework\Persistence\Statement
     */
    public function save(AbstractModel $entity, bool $preventCacheClearing = false): \Neoflow\Framework\Persistence\Statement
    {
        if ($entity->id()) {
            return $this->update($entity, $preventCacheClearing);
        }

        return $this->insert($entity, $preventCacheClearing);
    }

    /**
     * Persist model entity.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return \Neoflow\Framework\Persistence\Statement
     */
    public function persist(AbstractModel $entity, bool $preventCacheClearing = false): \Neoflow\Framework\Persistence\Statement
    {
        return $this->save($entity, $preventCacheClearing);
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByAsc($column)
    {
        $this->query->orderByAsc($column);

        return $this;
    }

    /**
     * Add ORDER BY ASC for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function orderByDesc($column)
    {
        $this->query->orderByDesc($column);

        return $this;
    }

    /**
     * Add raw ORDER BY statement.
     *
     * @param string $statement
     *
     * @return self
     */
    public function orderByRaw($statement)
    {
        $this->query->orderByRaw($statement);

        return $this;
    }

    /**
     * Enable/disable caching.
     *
     * @param bool $enable
     *
     * @return self
     */
    public function caching($enable = true)
    {
        $this->query->caching($enable);

        return $this;
    }

    /**
     * Add LIMIT.
     *
     * @param string $limit
     *
     * @return self
     */
    public function limit($limit)
    {
        $this->query->limit($limit);

        return $this;
    }

    /**
     * Add OFFSET.
     *
     * @param string $offset
     *
     * @return self
     */
    public function offset($offset)
    {
        $this->query->offset($offset);

        return $this;
    }

    /**
     * Add raw WHERE condition.
     *
     * @param string $condition
     * @param array  $parameters
     *
     * @return self
     */
    public function whereRaw($condition, array $parameters = [])
    {
        $this->query->whereRaw($condition, $parameters);

        return $this;
    }

    /**
     * Add where condition.
     *
     * @param string $property
     * @param string $operator
     * @param mixed  $parameter
     *
     * @return self
     */
    public function where($property, $operator, $parameter)
    {
        $this->query->where($property, $operator, $parameter);

        return $this;
    }

    /**
     * Add GROUP BY for column.
     *
     * @param string $column
     *
     * @return self
     */
    public function groupBy($column)
    {
        $this->query->groupBy($column);

        return $this;
    }

    /**
     * Find many model entities.
     *
     * @return EntityCollection
     */
    public function fetchAll()
    {
        // Execute query
        $collection = $this->query->fetchAll();

        // Get collection class name
        $collectionClassName = $this->getCollectionClassName();

        $entityCollection = new $collectionClassName();

        // Reset entity repository
        $this->reset();

        // Create entity collection
        if ($collection instanceof Collection) {
            return $entityCollection->set($collection->toArray());

            $this->logger()->debug('Entity collection fetched', [
                'Result' => $entityCollection->count().' model entities collected',
            ]);
        }

        // Return result
        return $entityCollection;
    }

    /**
     * Find first model enity.
     *
     * @param string|int $id Identifier of model entity Identifier of model entity
     *
     * @return AbstractModel
     */
    public function fetch($id = 0)
    {
        // Execute query
        $result = $this->query->fetch($id);

        if ($result) {
            $this->logger()
                ->debug('Model entity fetched', [
                    'Type' => $result->getReflection()->getName(),
                    'ID' => $result->id(),
            ]);
        }

        // Reset entity repository
        $this->reset();

        // Return result
        return $result;
    }

    /**
     * Count model entities.
     *
     * @return int
     */
    public function count()
    {
        // Execute query
        $result = $this->query->count();

        // Reset entity repository
        $this->reset();

        // Return result
        return (int) $result;
    }

    /**
     * Get table name of model.
     *
     * @param string $modelClassName
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected function getTableName($modelClassName = null)
    {
        if (!$modelClassName) {
            $modelClassName = $this->modelClassName;
        }

        if (class_exists($modelClassName)) {
            return $modelClassName::$tableName;
        }

        throw new RuntimeException('Model class "'.$modelClassName.'" not found');
    }

    /**
     * Get collection class name of model.
     *
     * @param string $modelClassName
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected function getCollectionClassName($modelClassName = null)
    {
        if (!$modelClassName) {
            $modelClassName = $this->modelClassName;
        }

        if (class_exists($modelClassName)) {
            return $modelClassName::$collectionClassName;
        }

        throw new RuntimeException('Model class "'.$modelClassName.'" not found');
    }

    /**
     * Get primary key of model.
     *
     * @param string $modelClassName
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected function getPrimaryKey($modelClassName = null)
    {
        if (!$modelClassName) {
            $modelClassName = $this->modelClassName;
        }

        if (class_exists($modelClassName)) {
            return $modelClassName::$primaryKey;
        }

        throw new RuntimeException('Model class "'.$modelClassName.'" not found');
    }

    /**
     * Reset entity repository.
     */
    protected function reset()
    {
        $this->modelClassName = null;
        $this->query = new QueryBuilder();
    }
}
