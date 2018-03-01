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
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @param array  $tags
     *
     * @return bool
     */
    public function store(string $key, $data, int $ttl = 0, array $tags = []): bool;

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool;

    /**
     * Check whether cache value exists.
     *
     * @param string $key
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
