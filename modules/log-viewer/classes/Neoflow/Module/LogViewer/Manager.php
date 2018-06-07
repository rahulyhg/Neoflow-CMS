<?php

namespace Neoflow\Module\LogViewer;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{
    /**
     * Install Log Viewer module.
     *
     * @return bool
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * Uninstall Log Viewer module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * Update Log Viewer module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
