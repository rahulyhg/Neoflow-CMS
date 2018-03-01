<?php

namespace Neoflow\Framework\Persistence\Caching;

class DummyCache extends AbstractCache
{
    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch(string $key)
    {
        return false;
    }

    /**
     * Store cache value.
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @param array  $tags
     *
     * @return bool
     */
    public function store(string $key, $data, int $ttl = 0, array $tags = []): bool
    {
        return true;
    }

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return true;
    }

    /**
     * Check whether cache value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return false;
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return true;
    }
}
