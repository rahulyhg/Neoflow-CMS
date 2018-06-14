<?php

namespace Neoflow\Module\LogViewer;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{
    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * Update module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
