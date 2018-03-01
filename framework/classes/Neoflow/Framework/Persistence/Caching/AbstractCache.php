<?php

namespace Neoflow\Framework\Persistence\Caching;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Common\KeyTaggingTrait;

abstract class AbstractCache implements CacheInterface
{
    /**
     * Traits.
     */
    use AppTrait;
    use KeyTaggingTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = $this->fetch('cacheTags');

        $this->logger()->debug('Cache created', [
            'Type' => $this->getReflection()->getShortName(),
        ]);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->store('cacheTags', $this->tags);
    }

    /**
     * Delete cache values by tag.
     *
     * @param string $tag
     *
     * @return bool
     */
    public function deleteByTag(string $tag): bool
    {
        $keys = $this->getKeysFromTag($tag);
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return $this->deleteTag($tag);
    }

    /**
     * Fetch cache value by tag.
     *
     * @param string $tag
     *
     * @return array
     */
    public function fetchByTag(string $tag): array
    {
        $keys = $this->getKeysFromTag($tag);
        $cacheValues = [];
        foreach ($keys as $key) {
            $cacheValues[] = $this->fetch($key);
        }

        return $cacheValues;
    }

    /**
     * Clear tags.
     *
     * @return bool
     */
    public function clearTags(): bool
    {
        $this->tags = [];

        return true;
    }

    /**
     * Check whether cache value by tag exists.
     *
     * @param array $tag
     *
     * @return bool
     */
    public function existsByTag(string $tag): bool
    {
        $cacheValues = $this->fetchByTag($tag);

        return count($cacheValues) > 0;
    }
}
