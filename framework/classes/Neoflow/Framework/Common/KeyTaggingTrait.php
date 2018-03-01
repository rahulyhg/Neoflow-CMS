<?php

namespace Neoflow\Framework\Common;

trait KeyTaggingTrait
{
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * Set key to tags.
     *
     * @param array  $tags
     * @param string $key
     */
    protected function setKeyToTags(array $tags, $key)
    {
        foreach ($tags as $tag) {
            if (!isset($this->tags[$tag])) {
                $this->tags[$tag] = [];
            }
            $this->tags[$tag][] = $key;
        }
    }

    /**
     * Get keys by tag.
     *
     * @param string $tag
     *
     * @return array
     */
    protected function getKeysFromTag($tag)
    {
        if (isset($this->tags[$tag])) {
            return $this->tags[$tag];
        }

        return [];
    }

    /**
     * Delete tags and tagged keys.
     *
     * @param string $tags
     *
     * @return bool
     */
    protected function deleteTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->deleteTag($tag);
        }

        return true;
    }

    /**
     * Delete tag and tagged keys.
     *
     * @param string $tag
     *
     * @return bool
     */
    protected function deleteTag($tag)
    {
        if (isset($this->tags[$tag])) {
            unset($this->tags[$tag]);
        }

        return true;
    }
}
