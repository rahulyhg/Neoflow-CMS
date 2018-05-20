<?php

namespace Neoflow\Module\CodeMirror;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{
    /**
     * Install CodeMirror module.
     *
     * @return bool
     */
    public function install(): bool
    {
        // Nothing todo
        return true;
    }

    /**
     * Uninstall CodeMirror module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        // Nothing todo
        return true;
    }

    /**
     * Initialize CodeMirror module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Register service
        $this->app()->get('services')->set('code', new Service($this->module));

        return true;
    }

    /**
     * Update CodeMirror module.
     *
     * @return bool
     */
    public function update(): bool
    {
        // Nothing todo
        return true;
    }
}
