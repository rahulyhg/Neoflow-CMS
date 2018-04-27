<?php

namespace Neoflow\Framework\Common;

trait KeyTaggingTrait
{
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * Map key to tags.
     *
     * @param array  $tags Tags to map key with
     * @param string $key  Key for tag mapping
     *
     * @return self
     */
    protected function mapKeyToTags(array $tags, string $key): self
    {
        foreach ($tags as $tag) {
            if (!isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }
            $this->tags[$tag][] = $key;
        }

        return $this;
    }

    /**
     * Get keys by tag.
     *
     * @param string $tag Tag
     *
     * @return array
     */
    protected function getKeysFromTag($tag): array
    {
        if (isset($this->tags[$tag])) {
            return $this->tags[$tag];
        }

        return [];
    }

    /**
     * Delete tags and tagged keys.
     *
     * @param string $tags List of tags
     *
     * @return bool
     */
    protected function deleteTags(array $tags): bool
    {
        foreach ($tags as $tag) {
            $this->deleteTag($tag);
        }

        return true;
    }

    /**
     * Delete tag and tagged keys.
     *
     * @param string $tag Tag
     *
     * @return bool
     */
    protected function deleteTag($tag): bool
    {
        if (isset($this->tags[$tag])) {
            unset($this->tags[$tag]);
        }

        return true;
    }
}
