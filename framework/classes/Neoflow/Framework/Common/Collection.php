<?php

namespace Neoflow\Framework\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use Neoflow\Framework\Core\AbstractModel;

class Collection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get item by index.
     *
     * @param int $index
     *
     * @return mixed
     */
    public function getByIndex(int $index)
    {
        if ($this->existsByIndex($index)) {
            return $this->items[$index];
        }

        return null;
    }

    /**
     * Delete item by index.
     *
     * @param type $index
     *
     * @return bool
     */
    public function deleteByIndex($index)
    {
        if ($this->exists($index)) {
            unset($this->items[$index]);
        }

        return true;
    }

    /**
     * Check wehter item exist by index.
     *
     * @param index $index
     *
     * @return bool
     */
    public function existsByIndex($index)
    {
        return isset($this->items[$index]);
    }

    /**
     * Apply callback to each collection items.
     *
     * @param callable $callback
     * @param mixed    $data     optional data parameter as third parameter for the callback
     *
     * @return self;
     *
     * @throws InvalidArgumentException
     */
    public function each($callback, $data = null)
    {
        if (is_callable($callback)) {
            array_walk_recursive($this->items, $callback, $data);

            return $this;
        }
        throw new InvalidArgumentException('Callback for each collection item is not callable');
    }

    /**
     * Filter collection items where are matching.
     *
     * @param string $property
     * @param string $value
     *
     * @return self
     */
    public function where($property, $value)
    {
        return $this->filter(function ($item) use ($property, $value) {
            return $item->{$property} == $value;
        });
    }

    /**
     * Add item to collection.
     *
     * @param mixed $item
     *
     * @return self
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Add multiple items to collection.
     *
     * @param array $items
     *
     * @return self
     */
    public function addMultiple(array $items): self
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * Merge collections.
     *
     * @param Collection $collection
     *
     * @return self
     */
    public function merge(self $collection): self
    {
        $this->addMultiple($collection->toArray());

        return $this;
    }

    /**
     * Add item as first collection item.
     *
     * @param mixed $item
     *
     * @return self
     */
    public function addFirst(AbstractModel $item)
    {
        array_unshift($this->items, $item);

        return $this;
    }

    /**
     * Filter collection items where are not matching.
     *
     * @param string $property
     * @param string $value
     *
     * @return self
     */
    public function whereNot($property, $value)
    {
        return $this->filter(function ($item) use ($property, $value) {
            return $item->{$property} != $value;
        });
    }

    /**
     * Apply callback to filters collection items.
     *
     * @param callable $callback
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function filter($callback)
    {
        if (is_callable($callback)) {
            $result = array_filter($this->items, $callback);

            return new static($result);
        }
        throw new InvalidArgumentException('Callback to filter collection items is not callable');
    }

    /**
     * Apply mapper callback to collection items.
     *
     * @param callable $callback
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function map($callback)
    {
        if (is_callable($callback)) {
            if ($this->count()) {
                return array_map($callback, $this->items);
            }

            return [];
        }
        throw new InvalidArgumentException('Callback for mapping collection items is not callable');
    }

    /**
     * Apply mapper method call to collection items.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws InvalidArgumentException
     */
    public function mapMethod(string $method, array $args = []): array
    {
        if ($this->count()) {
            if (method_exists($this->getByIndex(0), $method)) {
                $callback = function ($entity) use ($method, $args) {
                    return call_user_func_array(array($entity, $method), $args);
                };

                return $this->map($callback);
            }
            throw new InvalidArgumentException('Method for mapping collection items is not found');
        }

        return [];
    }

    /**
     * Apply mapper property to collection items.
     *
     * @param string $property
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function mapProperty(string $property): array
    {
        if ($this->count()) {
            if (property_exists($this->getByIndex(0), $property)) {
                $callback = function ($entity) use ($callback) {
                    return $entity->$callback;
                };

                return $this->map($callback);
            }
            throw new InvalidArgumentException('Property for mapping collection items not found');
        }

        return [];
    }

    /**
     * Join collection items to a string.
     *
     * @param callable $callback
     * @param string   $seperator
     *
     * @return string
     */
    public function implode($callback, $seperator = ', ')
    {
        $result = $this->map($callback);

        return implode($seperator, $result);
    }

    /**
     * Get first collection item.
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * Reverse order of collection items.
     *
     * @return self
     */
    public function reverse()
    {
        $this->items = array_reverse($this->items, true);

        return $this;
    }

    /**
     * Get last collection item.
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Get the collection of items as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Set an array of items to the collection.
     *
     * @param array $items
     *
     * @return self
     */
    public function set(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the collection of items as json.
     *
     * @param int $options
     * @param int $depth
     *
     * @return string
     */
    public function toJson($options = 0, $depth = 512)
    {
        return json_encode($this->items, $options, $depth);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $offset
     * @param mixed $item
     */
    public function offsetSet($offset, $item)
    {
        if (is_null($offset)) {
            $this->items[] = $item;
        } else {
            $this->items[$offset] = $item;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Convert collection into array to get JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * Sort entities by entity property.
     *
     * @param string $property
     * @param string $order
     *
     * @return self
     */
    public function sort($property, $order = 'ASC')
    {
        usort($this->items, function ($a, $b) use ($property) {
            if (method_exists($a, $property)) {
                return strcmp($a->{$property}(), $b->{$property}());
            }

            return strcmp($a->{$property}, $b->{$property});
        });
        if ('DESC' === $order) {
            $this->items = array_reverse($this->items);
        }

        return $this;
    }

    /**
     * Extract a slice of the collection items.
     *
     * @param int $length
     * @param int $offset
     *
     * @return self
     */
    public function slice($length, $offset = 0)
    {
        if (is_int($length) && $length > 0) {
            $this->items = array_slice($this->items, $offset, $length);
        }

        return $this;
    }
}
