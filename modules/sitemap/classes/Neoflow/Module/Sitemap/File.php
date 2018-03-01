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
     * Get sitemap url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->config()->getUrl($this->getName());
    }
}
