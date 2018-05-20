<?php

namespace Neoflow\Module\TinyMCE;

use Neoflow\CMS\Manager\AbstractModuleManager;

class Manager extends AbstractModuleManager
{
    /**
     * Install TinyMCE module.
     *
     * @return bool
     */
    public function install(): bool
    {
        return true;
    }

    /**
     * Uninstall TinyMCE module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        return true;
    }

    /**
     * Initialize TinyMCE module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Register service
        $this->app()->get('services')->set('wysiwyg', new Service($this->module));

        return true;
    }

    /**
     * Update TinyMCE module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
