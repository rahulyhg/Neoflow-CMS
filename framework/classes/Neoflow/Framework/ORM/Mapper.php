<?php
namespace Neoflow\Framework\ORM;

use InvalidArgumentException;
use Neoflow\Framework\Core\AbstractModel;
use Neoflow\Module\WYSIWYG\Model;
use RuntimeException;

class Mapper
{

    /**
     * @var Repository
     */
    protected $repo;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->repo = new Repository();
    }

    /**
     * Manage one-to-one and one-to-many relations where the foreign key is on the base model entity.
     *
     * @param AbstractModel  $entity Model entity
     * @param string $associatedModelClassName Associated model class name
     * @param string $foreignKeyName Foreign key name
     *
     * @return Repository
     */
    public function belongsTo(AbstractModel $entity, string $associatedModelClassName, string $foreignKeyName): Repository
    {
        // Get primary key
        $associatedPrimaryKey = $this->getPrimaryKey($associatedModelClassName);

        // Prepare where statement for associated model
        $this->repo
            ->forModel($associatedModelClassName)
            ->where($associatedPrimaryKey, '=', $entity->$foreignKeyName);

        // Return entity repository
        return $this->repo;
    }

    /**
     * Manage one-to-many relations where the foreign key is on the associated model entity.
     *
     * @param AbstractModel  $entity Model entity
     * @param string $associatedModelClassName Associated model class name
     * @param string $foreignKeyName Foreign key name
     *
     * @return Repository
     */
    public function hasMany(AbstractModel $entity, string $associatedModelClassName, string $foreignKeyName): Repository
    {
        return $this->hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one relation where the foreign key
     * is on the associated model entity.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return Repository
     */
    public function hasOne($entity, $associatedModelClassName, $foreignKeyName): Repository
    {
        return $this->hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName);
    }

    /**
     * Manage one-to-one and one-to-many relations.
     *
     * @param Model  $entity
     * @param string $associatedModelClassName
     * @param string $foreignKeyName
     *
     * @return Repository
     */
    public function hasOneOrMany($entity, $associatedModelClassName, $foreignKeyName): Repository
    {
        //Set entity mapper for associated model, create where statement and return it
        return $this->repo->forModel($associatedModelClassName)->where($foreignKeyName, '=', $entity->id());
    }

    /**
     * Manage many-to-many relations trought join model.
     *
     * @param AbstractModel  $entity Model entity
     * @param string $associatedModelClassName Associated model class name
     * @param string $joinModelClassName Join model class name
     * @param string $foreignKeyToBaseModel Foreign key to the base model
     * @param string $foreignKeyToAssociatedModel Foreign key to the associated model
     *
     * @return Repository
     */
    public function hasManyThrough(AbstractModel $entity, string $associatedModelClassName, string $joinModelClassName, string $foreignKeyToBaseModel, string $foreignKeyToAssociatedModel): Repository
    {
        // Get table names for each class
        $associatedTableName = $this->getTableName($associatedModelClassName);
        $joinTableName = $this->getTableName($joinModelClassName);

        // Get primary key
        $associatedPrimaryKey = $this->getPrimaryKey($associatedModelClassName);

        // Prepare join statment for associated model
        $this->repo
            ->forModel($associatedModelClassName)
            ->getQuery()
            ->innerJoin($joinTableName, $associatedTableName . '.' . $associatedPrimaryKey . ' = ' . $joinTableName . '.' . $foreignKeyToAssociatedModel)
            ->where($joinTableName . '.' . $foreignKeyToBaseModel, '=', $entity->id());

        // Return entity repository
        return $this->repo;
    }

    /**
     * Get table name of model.
     *
     * @param string $modelClassName Model class name
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function getTableName(string $modelClassName): string
    {
        if (class_exists($modelClassName)) {
            return $modelClassName::$tableName;
        }

        throw new RuntimeException('Model class ' . $modelClassName . ' not found');
    }

    /**
     * Get primary key of model.
     *
     * @param string $modelClassName Model class name
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getPrimaryKey(string $modelClassName): string
    {
        if (class_exists($modelClassName)) {
            return $modelClassName::$primaryKey;
        }

        throw new RuntimeException('Model class "' . $modelClassName . '" not found');
    }
}
