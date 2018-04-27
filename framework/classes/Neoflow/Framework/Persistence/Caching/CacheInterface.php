<?php

namespace Neoflow\Framework\Persistence\Caching;

interface CacheInterface
{
    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch(string $key);

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
    public function store(string $key, $data, int $ttl = 0, array $tags = []): bool;

    /**
     * Delete cache value.
     *
     * @param string $key Cache key
     *
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * Check whether cache value exists.
     *
     * @param string $key Cache key
     *
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear(): bool;
}
