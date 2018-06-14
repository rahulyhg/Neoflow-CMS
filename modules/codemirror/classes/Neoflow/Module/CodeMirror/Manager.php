<?php

namespace Neoflow\Module\CodeMirror;

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
        $this->app()->get('services')->set('code', new Service($this->module));

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
