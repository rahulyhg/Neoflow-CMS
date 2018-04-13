<?php

namespace Neoflow\Framework\Persistence\Caching;

class ApcuCache extends AbstractCache
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
        return \apcu_fetch($key);
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
        // Set key to tags
        $this->setKeyToTags($tags, $key);

        return \apcu_store($key, $data, $ttl);
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
        return \apcu_delete($key);
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
