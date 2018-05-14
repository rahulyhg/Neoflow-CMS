<?php
namespace Neoflow\CMS;

use Neoflow\CMS\Manager\AbstractUpdateManager;
use Neoflow\Filesystem\Folder;

class UpdateManager extends AbstractUpdateManager
{

    /**
     * Constructor.
     *
     * @param Folder $folder Update folder
     * @param array  $info   Info data
     */
    public function __construct(Folder $folder, array $info)
    {
        parent::__construct($folder, $info);

        // Fix for old info path config (only needed for update to 1.0.0-a2)
        $this->info['sql'] = $info['path']['sql'];
        $this->info['files'] = $info['path']['files'];
    }

    /**
     * Old istall method (only needed for update from 1.0.0-a1 to 1.0.0-a2).
     *
     * @return bool
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * Update files.
     *
     * @return bool
     */
    public function updateFiles(string $sqlFilePath): bool
    {
        if (parent::updateFiles($sqlFilePath)) {
            if (parent::install()) {
                $frameworkFolder = new Folder($this->config()->getPath('/framework'));

                return $frameworkFolder->delete();
            }

            return false;
        }
    }
}
