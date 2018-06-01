<?php

namespace Neoflow\Module\Sitemap;

use Neoflow\CMS\Manager\AbstractModuleManager;
use Neoflow\Module\Sitemap\Model\SettingModel;

class Manager extends AbstractModuleManager
{
    /**
     * Install Sitemap module.
     *
     * @return bool
     */
    public function install(): bool
    {
        $this
            ->database()
            ->prepare('
                    CREATE TABLE `mod_sitemap_urls` (
                        `url_id` INT NOT NULL AUTO_INCREMENT,
                        `loc` VARCHAR(255) NOT NULL,
                        `lastmod` VARCHAR(20) NOT NULL,
                        `changefreq` enum("always","hourly","daily","weekly","monthly","yearly","never") NOT NULL DEFAULT "monthly",
                        `priority` VARCHAR(5) NOT NULL,
                    PRIMARY KEY (`url_id`));

                    CREATE TABLE `mod_sitemap_settings` (
                        `setting_id` INT NOT NULL AUTO_INCREMENT,
                        `default_changefreq` enum("always","hourly","daily","weekly","monthly","yearly","never") NOT NULL DEFAULT "monthly",
                        `default_priority` VARCHAR(5) NOT NULL DEFAULT "0.5",
                        `sitemap_lifetime` INT NOT NULL DEFAULT "72",
                        `automated_creation` TINYINT(1) NOT NULL DEFAULT "1",
                    PRIMARY KEY (`setting_id`));
            ')
            ->execute();

        SettingModel::create([
            'default_changefreq' => 'monthly',
            'default_priority' => '0.5',
        ])->save();

        return true;
    }

    /**
     * Uninstall Sitemap module.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if ($this->database()->hasTable('mod_sitemap_urls')) {
            $this
                ->database()
                ->prepare('DROP TABLE `mod_sitemap_urls`')
                ->execute();
        }

        if ($this->database()->hasTable('mod_sitemap_settings')) {
            $this
                ->database()
                ->prepare('DROP TABLE `mod_sitemap_settings`')
                ->execute();
        }

        return true;
    }

    /**
     * Initialize Sitemap module.
     *
     * @return bool
     */
    public function initialize(): bool
    {
        // Create service
        $service = new Service($this->module);

        // Register service
        $this->app()->get('services')->set('sitemap', $service);

        if ($service->getSettings()->automated_creation) {
            // Check whether sitemap can be deleted if older than specified lifetime
            $sitemapFile = $service->getFile();
            if ($sitemapFile) {
                $modificationTime = $sitemapFile->getModificationTime();
                $sitemapLifetime = $service->getSettings()->getSitemapLifetime('seconds');

                if ((time() - $modificationTime) > $sitemapLifetime) {
                    if ($sitemapFile->delete()) {
                        $sitemapFile = null;
                    }
                }
            }

            // Create sitemap
            if (!$sitemapFile) {
                $service->generateAsFile();
            }
        }

        return true;
    }

    /**
     * Update Sitemap module.
     *
     * @return bool
     */
    public function update(): bool
    {
        return true;
    }
}
