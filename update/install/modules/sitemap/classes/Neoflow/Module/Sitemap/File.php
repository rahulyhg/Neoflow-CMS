<?php

namespace Neoflow\Module\Sitemap;

use Neoflow\Filesystem\File as FrameworkFile;

class File extends FrameworkFile
{
    /**
     * App trait.
     */
    use \Neoflow\CMS\AppTrait;

    /**
     * Get sitemap URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->config()->getUrl($this->getName());
    }
}
