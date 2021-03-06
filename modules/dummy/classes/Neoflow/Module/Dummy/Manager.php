<?php

namespace Neoflow\Module\Dummy;

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
        // Create tables

        return true;
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        // Drop tables

        return true;
    }

    /**
     * Initialize module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Initialize module (e.g. register service to App instance)

        return true;
    }

    /**
     * Update module.
     *
     * @return bool
     */
    public function update(): bool
    {
        // Alter tables
        if ('1.0.0' === $this->module->oldVersion) {
            $this->logger()->info('Dummy update of Dummy module :)');
        }

        return true;
    }
}
