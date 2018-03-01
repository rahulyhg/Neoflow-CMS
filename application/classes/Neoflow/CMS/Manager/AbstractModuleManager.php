<?php

namespace Neoflow\CMS\Manager;

use Neoflow\CMS\Model\ModuleModel;
use Neoflow\Framework\AppTrait;

abstract class AbstractModuleManager
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var ModuleModel
     */
    protected $module;

    /**
     * Constructor.
     *
     * @param ModuleModel $module
     */
    public function __construct(ModuleModel $module)
    {
        $this->module = $module;
    }

    /**
     * Install module.
     */
    abstract public function install(): bool;

    /**
     * Uninstall module.
     */
    abstract public function uninstall(): bool;

    /**
     * Uninstall module.
     */
    abstract public function update(): bool;

    /**
     * Initialize module (after application is initialized).
     *
     * @return bool
     */
    public function initialize(): bool
    {
        return true;
    }

    /**
     * Execute module (after application is executed).
     *
     * @return bool
     */
    public function execute(): bool
    {
        return true;
    }
}
