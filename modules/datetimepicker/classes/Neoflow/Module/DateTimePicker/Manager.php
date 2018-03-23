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
        $this->app()->registerService(new Service($this->module), 'datetimepicker');

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
