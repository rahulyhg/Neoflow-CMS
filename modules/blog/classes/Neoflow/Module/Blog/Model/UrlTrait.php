<?php

namespace Neoflow\Module\Blog\Model;

trait UrlTrait
{
    /**
     * Get URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        $page = $this->getSection()->getPage();

        if ($page) {
            return normalize_url($page->getUrl().'/'.$this->title_slug);
        }

        return '#';
    }

    /**
     * Save model entity.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        $this->title_slug = slugify($this->title);

        return parent::save($preventCacheClearing);
    }
}
