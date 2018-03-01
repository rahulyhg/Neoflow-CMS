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
     * Unintall TinyMCE module.
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
        $this->app()->registerService(new Service($this->module), 'wysiwyg');

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
