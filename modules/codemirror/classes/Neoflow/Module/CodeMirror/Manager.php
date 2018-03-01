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
     * Unintall CodeMirror module.
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
        $this->app()->registerService(new Service($this->module), 'code');

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
