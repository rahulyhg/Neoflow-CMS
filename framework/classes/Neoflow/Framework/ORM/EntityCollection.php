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
     * Delete model entities in collection.
     *
     * @return bool
     */
    public function delete()
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
     * @param string $key
     *
     * @return array
     */
    public function mapValue(string $key)
    {
        $callback = function ($entity) use ($key) {
            return $entity->$key;
        };

        return $this->map($callback);
    }

    /**
     * Join collection values to a string.
     *
     * @param string $key
     * @param string $seperator
     *
     * @return string
     */
    public function implodeValue(string $key, string $seperator = ', ')
    {
        $callback = function ($entity) use ($key) {
            return $entity->$key;
        };

        return $this->implode($callback, $seperator);
    }
}
