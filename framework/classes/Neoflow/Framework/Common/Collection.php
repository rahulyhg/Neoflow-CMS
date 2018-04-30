<?php
namespace Neoflow\Framework\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;

class Collection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable
{

    /**
     * Collection item type.
     *
     * @var string
     */
    protected static $className = '';

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
        $this->set($items);
    }

    /**
     * Get item by index.
     *
     * @param int $index Item index
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
     * @param int $index Item index
     *
     * @return bool
     */
    public function deleteByIndex(int $index): bool
    {
        if ($this->exists($index)) {
            unset($this->items[$index]);
        }

        return true;
    }

    /**
     * Check whether item exist by index.
     *
     * @param int $index Item index
     *
     * @return bool
     */
    public function existsByIndex(index $index): bool
    {
        return isset($this->items[$index]);
    }

    /**
     * Apply callback to each collection items.
     *
     * @param callable $callback Each callback
     * @param mixed    $data     Optional parameters for the callback
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function each(callable $callback, $data = null): self
    {
        array_walk_recursive($this->items, $callback, $data);

        return $this;
    }

    /**
     * Filter collection items where are matching.
     *
     * @param string $property Item property
     * @param mixed  $value    Property value
     *
     * @return self
     */
    public function where(string $property, $value): self
    {
        return $this->filter(function ($item) use ($property, $value) {
                return $item->{$property} == $value;
            });
    }

    /**
     * Add item to collection.
     *
     * @param mixed $item Collection item
     *
     * @return self
     */
    public function add($item): self
    {
        $this->validate($item);
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
        foreach ($items as $item) {
            $this->validate($item);
        }

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
     * @param mixed $item Collection item
     *
     * @return self
     */
    public function addFirst($item): self
    {
        $this->validate($item);

        array_unshift($this->items, $item);

        return $this;
    }

    /**
     * Filter collection items where are not matching.
     *
     * @param string $property Item property
     * @param mixed  $value    Property value
     *
     * @return self
     */
    public function whereNot(string $property, $value): self
    {
        return $this->filter(function ($item) use ($property, $value) {
                return $item->{$property} != $value;
            });
    }

    /**
     * Apply callback to filters collection items.
     *
     * @param callable $callback Filter callback
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function filter(callable $callback): self
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
     * @param callable $callback Mapper callback
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function map(callable $callback): array
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
                    return call_user_func_array([$entity, $method], $args);
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
     * @param callable $callback  Implode callback
     * @param string   $seperator String seperator
     *
     * @return string
     */
    public function implode(callable $callback, $seperator = ', '): string
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
    public function reverse(): self
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
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get the collection of items as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Set a list of items to the collection.
     *
     * @param array $items List of items
     *
     * @return self
     */
    public function set(array $items): self
    {
        foreach ($items as $item) {
            $this->validate($item);
        }

        $this->items = $items;

        return $this;
    }

    /**
     * Validate item.
     *
     * @param mixed $item Collection item
     *
     * @throw InvalidArgumentException
     *
     * @return self
     */
    protected function validate($item): self
    {
        if (static::$className && !is_a($item, static::$className)) {
            throw new InvalidArgumentException('Collection item is not valid and has to be an instance of "' . static::$className . '"');
        }

        return $this;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Check whether item exists, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Get item by offset, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * Set item, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     * @param mixed $item   Collection item
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
     * Unset/delete item by offset, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Sort entities by entity property.
     *
     * @param string $property
     * @param string $order
     *
     * @return self
     */
    public function sort($property, $order = 'ASC'): self
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
     * @param int $length Slice length
     * @param int $offset Offset of slice start
     *
     * @return self
     */
    public function slice(int $length, int $offset = 0): self
    {
        if (is_int($length) && $length > 0) {
            $this->items = array_slice($this->items, $offset, $length);
        }

        return $this;
    }

    /**
     * Serialize collection.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->items);
    }
}
