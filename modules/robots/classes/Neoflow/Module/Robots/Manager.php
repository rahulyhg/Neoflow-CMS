<?php

namespace Neoflow\Module\Robots;

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
     * Initialize module.
     *
     * @return bool
     */
    public function initialize(): bool
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
        if ('1.0.0' === $this->module->oldVersion) {
            echo $this->module->oldVersion;
            exit;
        }

        return true;
    }
}
