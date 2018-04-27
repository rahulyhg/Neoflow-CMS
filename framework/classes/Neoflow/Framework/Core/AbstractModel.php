<?php

namespace Neoflow\Framework\Core;

use InvalidArgumentException;
use Neoflow\Framework\AppTrait;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\Mapper;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Framework\Persistence\QueryBuilder;
use Neoflow\Framework\Persistence\Querying\SelectQuery;
use RuntimeException;

abstract class AbstractModel
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Table name of model entity.
     *
     * @var string
     */
    public static $tableName;

    /**
     * Primary key of model entity.
     *
     * @var string
     */
    public static $primaryKey;

    /**
     * Properties of model entity.
     *
     * @var array
     */
    public static $properties = [];

    /**
     * Collection class name of model entity.
     *
     * @var string
     */
    public static $collectionClassName = '\\Neoflow\\Framework\\ORM\\EntityCollection';

    /**
     * Mapper of model entity.
     *
     * @var Mapper
     */
    protected $mapper;

    /**
     * Data of model entity.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Modified properties of model entity.
     *
     * @var array
     */
    protected $modifiedProperties = [];

    /**
     * State whether model entity is read-only.
     *
     * @var bool
     */
    protected $isReadOnly = false;

    /**
     * State whether model entity is modified.
     *
     * @var bool
     */
    protected $isModified = false;

    /**
     * @var bool
     */
    protected $isNew = true;

    /**
     * Constructor.
     *
     * @param array $data       Data of model entity
     * @param bool  $isReadOnly State whether model entity is read-only or not
     */
    public function __construct(array $data = [], $isReadOnly = false)
    {
        $this->setData($data, true);

        $this->mapper = new Mapper();

        $this->isReadOnly = $isReadOnly;

        $this->modifiedProperties = [];
        $this->isModified = false;

        if ($this->id()) {
            $this->isNew = false;
        }
    }

    /**
     * Get table name of model entity.
     *
     * @return string
     */
    protected function getTableName(): string
    {
        $modelClassName = get_class($this);

        return $modelClassName::$tableName;
    }

    /**
     * Get collection class name of model entity.
     *
     * @return string
     */
    protected function getCollectionClassName(): string
    {
        $modelClassName = get_class($this);

        return $modelClassName::$collectionClassName;
    }

    /**
     * Get primary key of model entity.
     *
     * @return string
     */
    protected function getPrimaryKey(): string
    {
        $modelClassName = get_class($this);

        return $modelClassName::$primaryKey;
    }

    /**
     * Get properties of model entity.
     *
     * @return string
     */
    protected function getProperties(): array
    {
        $modelClassName = get_class($this);

        return $modelClassName::$properties;
    }

    /**
     * Get data of model entity as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Return id of model entity.
     *
     * @return int|null
     */
    public function id()
    {
        $primaryKey = $this->getPrimaryKey();
        $id = $this->{$primaryKey};
        if ($id) {
            return (int) $id;
        }

        return null;
    }

    /**
     * Check whether model entity is read-only.
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * Check whether data of model entity has changed.
     *
     * @return bool
     */
    public function isModified(): bool
    {
        return $this->isModified;
    }

    /**
     * Set read-only.
     *
     * @param bool $isReadOnly Set FALSE to disable read-only
     *
     * return self
     */
    public function setReadOnly(bool $isReadOnly = true): self
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Set model entity value.
     *
     * @param string $property Entity property
     * @param mixed  $value    Property value
     * @param bool   $silent   Set TRUE to prevent the tracking of the change
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function set(string $property, $value = null, bool $silent = false): self
    {
        if ($this->isReadOnly()) {
            throw new RuntimeException('Model entity is read only and cannot set value');
        }

        if (in_array($property, $this->getProperties())) {
            $this->data[$property] = $value;

            if (!$silent) {
                $this->modifiedProperties[] = $property;
                $this->isModified = true;
            }
        } else {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Check whether model entity property exists.
     *
     * @param string $property Entity property
     *
     * @return bool
     */
    public function exists($property): bool
    {
        return isset($this->data[$property]);
    }

    /**
     * Get model entity value.
     *
     * @param string $property Entity property
     * @param mixed  $default  Default return value when key doesn't exists
     *
     * @return mixed
     */
    public function get(string $property, $default = null)
    {
        if ($this->exists($property)) {
            return $this->data[$property];
        }

        return $default;
    }

    /**
     * Remove model entity value.
     *
     * @param string $property Entity property
     *
     * @return self
     */
    protected function remove($property): self
    {
        if ($this->exists($property)) {
            unset($this->data[$property]);
        }

        if (false !== ($index = array_search($property, $this->modifiedProperties))) {
            unset($this->modifiedProperties[$index]);
        }

        return $this;
    }

    /**
     * Validate model entity.
     *
     * @return bool
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Setter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Create model entity.
     *
     * @param array $data Data of model entity
     *
     * @return static
     */
    public static function create(array $data): self
    {
        return new static($data);
    }

    /**
     * Isset.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Set data and save model entity.
     *
     * @param array $data Data for model entity
     *
     * @return self
     */
    public function update(array $data): self
    {
        $this
            ->setData($data)
            ->save();

        return $this;
    }

    /**
     * Set data of model entity.
     *
     * @param array $data   Entity data
     * @param bool  $silent Set TRUE to prevent the tracking of the change
     *
     * @return self
     */
    public function setData(array $data, bool $silent = false): self
    {
        foreach ($data as $property => $value) {
            $this->set($property, $value, $silent);
        }

        return $this;
    }

    /**
     * Update model entity by id.
     *
     * @param array  $data Data for model entity
     * @param string $id   Identifier of model entity
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public static function updateById(array $data, int $id): self
    {
        $entity = static::findById($id);
        if ($entity) {
            $entity->setData($data);

            return $entity;
        }
        throw new InvalidArgumentException('Model entity not found (ID: '.$id.')');
    }

    /**
     * Save model entity.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        if ($this->isModified || $this->isNew) {
            if ($this->id()) {
                return (bool) static::repo()->update($this, $preventCacheClearing);
            }

            if (static::repo()->persist($this, $preventCacheClearing)) {
                $id = $this->database()->lastInsertId();

                if ($id) {
                    $primaryKey = $this->getPrimaryKey();
                    $this->set($primaryKey, $id);

                    return true;
                }
            }

            return false;
        }

        return true;
    }

    /**
     * Get data of model entity.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->toArray();
    }

    /**
     * Get modified data of model entity.
     *
     * @return array
     */
    public function getModifiedData(): array
    {
        return array_intersect_key($this->data, array_flip($this->modifiedProperties));
    }

    /**
     * Delete model entity.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if ($this->id()) {
            return static::repo()->delete($this);
        }

        return true;
    }

    /**
     * Manage one-to-one and one-to-many relations where the foreign key is on the base model entity.
     *
     * @param string $associatedModelClassName Associated model class name
     * @param string $foreignKeyName           Foreign key name
     *
     * @return Repository
     */
    protected function belongsTo(string $associatedModelClassName, string $foreignKeyName): Repository
    {
        return $this->mapper->belongsTo($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one relation where the foreign key is on the associated model entity.
     *
     * @param string $associatedModelClassName Associated model class name
     * @param string $foreignKeyName           Foreign key name
     *
     * @return Repository
     */
    protected function hasOne(string $associatedModelClassName, string $foreignKeyName): Repository
    {
        return $this->mapper->hasOne($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-many relations where the foreign key is on the associated model entity.
     *
     * @param string $associatedModelClassName Associated model class name
     * @param string $foreignKeyName           Foreign key name
     *
     * @return Repository
     */
    protected function hasMany(string $associatedModelClassName, string $foreignKeyName): Repository
    {
        return $this->mapper->hasMany($this, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage many-to-many relations trought join model.
     *
     * @param string $associatedModelClassName    Associated model class name
     * @param string $joinModelClassName          Join model class name
     * @param string $foreignKeyToBaseModel       Foreign key to the base model
     * @param string $foreignKeyToAssociatedModel Foreign key to the associated model
     *
     * @return Repository
     */
    protected function hasManyThrough(string $associatedModelClassName, string $joinModelClassName, string $foreignKeyToBaseModel, string $foreignKeyToAssociatedModel): Repository
    {
        return $this->mapper->hasManyThrough($this, $associatedModelClassName, $joinModelClassName, $foreignKeyToBaseModel, $foreignKeyToAssociatedModel);
    }

    /**
     * Add additional property to model entity.
     *
     * @param string $property Entity property
     *
     * @return self
     */
    public function addProperty(string $property): self
    {
        $modelClassName = get_class($this);

        $modelClassName::$properties[] = $property;

        return $this;
    }

    /**
     * Remove property from model entity.
     *
     * @param string $property Entity property
     *
     * @return self
     */
    public function removeProperty(string $property): self
    {
        $modelClassName = get_class($this);

        if (false !== ($index = array_search($property, $modelClassName::$properties))) {
            unset($modelClassName::$properties[$index]);
        }

        return $this->remove($property);
    }

    /**
     * Create repository for entity model.
     *
     * @return Repository
     */
    public static function repo(): Repository
    {
        $repo = new Repository();

        return $repo->forModel(get_called_class());
    }

    /**
     * Create select query for entity model.
     *
     * @param array $columns
     *
     * @return SelectQuery
     */
    protected static function selectQuery(array $columns = []): SelectQuery
    {
        return static::queryBuilder()->selectFrom(static::$tableName, $columns);
    }

    /**
     * Get query builder.
     *
     * @return QueryBuilder
     */
    protected static function queryBuilder(): QueryBuilder
    {
        return new QueryBuilder();
    }

    /**
     * Delete model entity by id.
     *
     * @param string|int $id Identifier of model entity
     *
     * @return bool
     */
    public static function deleteById($id): bool
    {
        $entity = static::findById($id);
        if ($entity) {
            return $entity->delete();
        }

        return false;
    }

    /**
     * Delete all model entities by column.
     *
     * @param string $column
     * @param mixed  $value
     *
     * @return bool
     */
    public static function deleteAllByColumn(string $column, $value): bool
    {
        return static::findAllByColumn($column, $value)->delete();
    }

    /**
     * Find model entity by id.
     *
     * @param mixed $id Identifier of model entity
     *
     * @return static|null
     */
    public static function findById($id)
    {
        return static::repo()->fetch($id);
    }

    /**
     * Find model entity by column.
     *
     * @param string $column
     * @param mixed  $value
     *
     * @return static|null
     */
    public static function findByColumn(string $column, $value)
    {
        return static::repo()
                ->where($column, '=', $value)
                ->fetch();
    }

    /**
     * Find all model entities.
     *
     * @return EntityCollection
     */
    public static function findAll(): EntityCollection
    {
        return static::repo()->fetchAll();
    }

    /**
     * Find all model entities by column.
     *
     * @param string $column Calumn name
     * @param mixed  $value  Filter value
     *
     * @return EntityCollection
     */
    public static function findAllByColumn(string $column, $value): EntityCollection
    {
        return static::repo()
                ->where($column, '=', $value)
                ->fetchAll();
    }

    /**
     * Find all model entities by multiple columns.
     *
     * @param array $columns Column names with filter values (as array with name => value)
     *
     * @return EntityCollection
     */
    public static function findAllByColumns(array $columns): EntityCollection
    {
        $repo = static::repo();
        foreach ($columns as $column => $value) {
            $repo->where($column, '=', $value);
        }

        return $repo->fetchAll();
    }
}
