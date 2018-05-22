<?php

namespace Neoflow\Module\Search;

use Neoflow\CMS\Manager\AbstractModuleManager;
use Neoflow\Module\Search\Model\SettingModel;

class Manager extends AbstractModuleManager
{
    /**
     * Install module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $this->uninstall();

        $this
                ->database()
                ->prepare('CREATE TABLE `mod_search_entities` (
                                    `entity_id` INT NOT NULL AUTO_INCREMENT,
                                    `entity_class` VARCHAR(255) NOT NULL,
                                PRIMARY KEY (`entity_id`));')
                ->execute();

        $this
                ->database()
                ->prepare('CREATE TABLE `mod_search_settings` (
                                    `setting_id` INT NOT NULL AUTO_INCREMENT,
                                    `url_path` VARCHAR(200) NOT NULL DEFAULT "/search" ,
                                    `is_active` tinyint(1) NOT NULL DEFAULT 1,
                                PRIMARY KEY (`setting_id`));')
                ->execute();

        $setting = SettingModel::create([
                    'url_path' => '/search',
                    'is_active' => true,
        ]);

        return $setting->save();
    }

    /**
     * Uninstall module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if ($this->database()->hasTable('mod_search_entities')) {
            $this
                    ->database()
                    ->prepare('DROP TABLE `mod_search_entities`')
                    ->execute();
        }

        if ($this->database()->hasTable('mod_search_settings')) {
            $this
                    ->database()
                    ->prepare('DROP TABLE `mod_search_settings`')
                    ->execute();
        }

        return true;
    }

    /**
     * Initialize module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Create service
        $service = new Service($this->module);

        // Register service
        $this->app()->get('services')->set('search', $service);

        // Check whether search page is active and accessible
        if ($service->getSettings()->is_active) {
            // Add custom url path as route
            $this->router()->addRoutes([
                ['tmod_search_frontend_index', 'get', $service->getSettings()->url_path, 'Neoflow\\Module\\Search\\Controller\\Frontend@index'],
            ]);
        }

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
