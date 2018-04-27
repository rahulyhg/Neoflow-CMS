<?php

namespace Neoflow\Framework\Persistence\Caching;

class ApcuCache extends AbstractCache
{
    /**
     * Fetch cache value.
     *
     * @param string $key Cache key
     *
     * @return mixed
     */
    public function fetch(string $key)
    {
        return \apcu_fetch($key);
    }

    /**
     * Store cache value.
     *
     * @param string $key  Cache key
     * @param mixed  $data Cache data
     * @param int    $ttl  Cache lifetime
     * @param array  $tags Cache tags
     *
     * @return bool
     */
    public function store(string $key, $data, int $ttl = 0, array $tags = []): bool
    {
        // Set key to tags
        $this->mapKeyToTags($tags, $key);

        return \apcu_store($key, $data, $ttl);
    }

    /**
     * Delete cache value.
     *
     * @param string $key Cache key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return \apcu_delete($key);
    }

    /**
     * Check whether cache value exists.
     *
     * @param string $key Cache key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return \apcu_exists($key);
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->clearTags();

        return \apcu_clear_cache();
    }
}
