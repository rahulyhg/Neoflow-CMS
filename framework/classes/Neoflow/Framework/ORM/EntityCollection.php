<?php
namespace Neoflow\Framework\ORM;

use Neoflow\Framework\Common\Collection;

class EntityCollection extends Collection
{

    /**
     * Load app.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * Collection item type.
     *
     * @var string
     */
    protected static $className = '\\Neoflow\\Framework\\Core\\AbstractModel';

    /**
     * Delete model entities in collection.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $result = true;
        $this->each(function ($item) use ($result) {
            if (!$item->delete()) {
                $result = false;
            }
        });

        return $result;
    }

    /**
     * Apply mapper value to collection entities.
     *
     * @param string $property Entity property
     *
     * @return array
     */
    public function mapValue(string $property): array
    {
        $callback = function ($entity) use ($property) {
            return $entity->{$property};
        };

        return $this->map($callback);
    }

    /**
     * Join collection values to a string.
     *
     * @param string $property  Entity property
     * @param string $seperator Implode seperator
     *
     * @return string
     */
    public function implodeValue(string $property, string $seperator = ', '): string
    {
        $callback = function ($entity) use ($property) {
            return $entity->{$property};
        };

        return $this->implode($callback, $seperator);
    }
}
