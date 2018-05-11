<?php
namespace Neoflow\CMS;

use Neoflow\CMS\Manager\AbstractUpdateManager;
use Neoflow\Filesystem\Folder;

class UpdateManager extends AbstractUpdateManager
{

    /**
     * Install update.
     *
     * @return bool
     */
    public function install(): bool
    {
        if (parent::install()) {
            $frameworkFolder = new Folder($this->config()->getPath('/framework'));

            return $frameworkFolder->delete();
        }

        return false;
    }
}
