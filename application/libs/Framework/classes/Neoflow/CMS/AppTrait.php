<?php
namespace Neoflow\CMS;

use Neoflow\CMS\Handler\Config;
use Neoflow\CMS\Handler\Router;
use Neoflow\CMS\Handler\Translator;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\Framework\AppTrait as FrameworkAppTrait;
use Neoflow\Framework\ORM\EntityCollection;

/**
 * @method App        app()        Get application
 * @method Config     config()     Get config
 * @method Translator translator() Get translator
 * @method Router     router()     Get router
 */
trait AppTrait
{

    use FrameworkAppTrait;

    /**
     * Get CMS settings.
     *
     * @return SettingModel
     */
    public function settings(): SettingModel
    {
        return $this->app()->get('settings');
    }

    /**
     * Get active modules.
     *
     * @return EntityCollection
     */
    public function modules(): EntityCollection
    {
        return $this->app()->get('modules');
    }
}
