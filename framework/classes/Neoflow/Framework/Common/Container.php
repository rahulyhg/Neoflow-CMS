<?php

namespace Neoflow\Framework\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use RuntimeException;

class Container implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array container data
     */
    protected $data = [];

    /**
     * @var bool State whether the container is read-only
     */
    protected $isReadOnly = false;

    /**
     * @var bool State whether the container data is modified
     */
    protected $isModified = false;

    /**
     * @var bool whether the container data is multi-dimensional
     */
    protected $isMultiDimensional = false;

    /**
     * @var array parent key
     */
    protected $parents = [];

    /**
     * Constructor.
     *
     * @param array $data
     * @param bool  $isReadOnly
     * @param bool  $isMultiDimensional
     * @param string parent
     */
    public function __construct(array $data = [], $isReadOnly = false, $isMultiDimensional = false, $parent = '')
    {
        $this->isMultiDimensional = $isMultiDimensional;

        $this->setData($data);
        if ($parent) {
            $this->parents[] = $parent;
        }

        $this->isReadOnly = $isReadOnly;
    }

    /**
     * Set data.
     *
     * @param array $data
     *
     * @return self
     */
    public function setData(array $data): self
    {
        if (is_assoc($data)) {
            if ($this->isMultiDimensional) {
                foreach ($data as $key => $value) {
                    $this->set($key, $value);
                }
            } else {
                $this->data = $data;
            }
        }

        return $this;
    }

    /**
     * Get container data as an array.
     *
     * @param bool $recursively Set FALSE to prevent recursive array casting
     *
     * @return array
     */
    public function toArray(bool $recursively = true): array
    {
        $data = $this->data;

        if ($recursively) {
            foreach ($this->data as $key => $value) {
                if ($value instanceof self) {
                    $data[$key] = $value->toArray($recursively);
                }
            }
        }

        return $data;
    }

    /**
     * Get first data value.
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->data);
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
     * Get last data value.
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->data);
    }

    /**
     * Check whether the data container is read-only.
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * Check whether data has changed.
     *
     * @return bool
     */
    public function isModified(): bool
    {
        return $this->isModified;
    }

    /**
     * Set data value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function set(string $key, $value = null): self
    {
        if ($this->isReadOnly()) {
            throw new RuntimeException('Container is read only and cannot set data value');
        }
        if ($this->isMultiDimensional && is_array($value) && (0 === count($value) || is_assoc($value))) {
            $this->data[$key] = new static($value, $this->isReadOnly(), true, $key);
        } else {
            $this->data[$key] = $value;
        }
        $this->isModified = true;

        return $this;
    }

    /**
     * Chech whether container is child of parent container.
     *
     * @param array|string $parents
     *
     * @return bool
     */
    public function isChildOf($parents): bool
    {
        if (is_string($parents)) {
            return end($this->parents) === $parents;
        } elseif (is_array($parents)) {
            return $this->parents === $parents;
        }

        return false;
    }

    /**
     * Check whether data value exists.
     *
     * @param string $key Key of data value
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Delete data value.
     *
     * @param string $key Key of data value
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function delete(string $key): bool
    {
        if ($this->isReadOnly()) {
            throw new RuntimeException('Container is read only and cannot delete data value');
        }

        if ($this->exists($key)) {
            unset($this->data[$key]);
            $this->isModified = true;

            return true;
        }

        return false;
    }

    /**
     * Get data value.
     *
     * @param string $key     Key of data value
     * @param mixed  $default Default return value when key doesn't exists
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Number of data values, implements Countable.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get an iterator, implements IteratorAggregate.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Get data value.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Set data value.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Container
     */
    public function __set($key, $value)
    {
        return $this->set($key, $value, false);
    }

    /**
     * Remove data value.
     *
     * @param mixed $key
     */
    public function __unset($key)
    {
        $this->delete($key);
    }

    /**
     * Check if named data value exists.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->exists($key);
    }

    /**
     * Call for named get and set methods.
     *
     * @param string $key
     * @param array  $arguments
     *
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function __call($key, $arguments)
    {
        if (0 === mb_strpos($key, 'set')) {
            if (!isset($arguments[0])) {
                $arguments[] = [];
            }
            $key = mb_strtolower(str_replace(['set', 'set_'], '', $key));

            return $this->set($key, $arguments[0]);
        } elseif (0 === mb_strpos($key, 'get')) {
            $key = mb_strtolower(str_replace(['get', 'get_'], '', $key));

            return $this->get($key);
        }

        return null;
    }

    /**
     * Check whether value by exists, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * Get value by offset, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set value, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Unset/delete value by offset, implements ArrayAccess.
     *
     * @param mixed $offset Offset
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
