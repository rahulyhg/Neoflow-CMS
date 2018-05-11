<?php

namespace Neoflow\Module\Robots;

use Neoflow\Filesystem\File as FrameworkFile;

class File extends FrameworkFile
{
    /**
     * App trait.
     */
    use \Neoflow\CMS\AppTrait;

    /**
     * Get robots.txt URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->config()->getUrl($this->getName());
    }
}
