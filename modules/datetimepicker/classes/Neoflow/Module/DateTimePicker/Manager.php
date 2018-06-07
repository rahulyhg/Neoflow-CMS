<?php

namespace Neoflow\Module\DateTimePicker;

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
        return true;
    }

    /**
     * Uninstall CodeMirror module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
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
        $this->app()->get('services')->set('datetimepicker', new Service($this->module));

        return true;
    }

    /**
     * Update CodeMirror module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
