<?php

namespace Neoflow\CMS\Service;

use Exception;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Handler\Translator;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\Handler\Logging\Logger;
use Neoflow\Framework\Persistence\Database;
use RuntimeException;
use Throwable;
use const APP_MODE;

class InstallService extends AbstractService
{
    /**
     * Establish database connection, create tables and insert data.
     *
     * @param array $config Database configuration
     *
     * @return self
     */
    public function createDatabase(array $config): self
    {
        // Establish connection to database
        $database = Database::connect($config['host'], $config['dbname'], $config['username'], $config['password'], $config['charset']);
        $this->app()->set('database', $database);

        // Alter database to user defined charset
        if ($this->database()->hasGrants(['ALTER'])) {
            $this->database()->exec('ALTER DATABASE `'.$config['dbname'].'` CHARACTER SET '.strtolower($config['charset']));
        }

        // Create tables
        $createSqlFilePath = $this->config()->getPath('/installation/tables.sql');
        $this->database()->executeFile($createSqlFilePath);

        // Get SQL to insert data into tables
        $insertSqlFilePath = $this->config()->getPath('/installation/data.sql');
        $this->database()->executeFile($insertSqlFilePath);

        return $this;
    }

    /**
     * Install modules.
     *
     * @return self
     */
    public function installModules(): self
    {
        // Get modules folder
        $modulesFolder = new Folder($this->config()->getModulesPath());

        // Get installation info
        $info = include $this->config()->getPath('/installation/info.php');

        // Install each module package file
        foreach ($info['modules'] as $folderName) {
            $folder = $modulesFolder->findFolders($folderName)->first();
            if (!empty($folder)) {
                try {
                    // Create and validate module
                    $info = include $folder->getPath('info.php');
                    $module = ModuleModel::create($info);

                    // Save and install module
                    if ($module && $module->validate() && $module->loadClassesAndFunctions() && $module->save() && $module->getManager()->install()) {
                        $this->logger()->info($module->name.' successfully installed.');
                    } else {
                        $this->logger()->error('Installing module '.$module->name.' failed for unknown reasons.');
                    }
                } catch (Throwable $ex) {
                    $this->logger()->error('Module installation for '.$folder->getName().' failed.', ['Exception message' => $ex->getMessage()]);
                }
            } else {
                $this->logger()->warning('Module folder '.$folderName.' not found.');
            }
        }

        return $this;
    }

    /**
     * Install themes.
     *
     * @return self
     */
    public function installThemes(): self
    {
        // Get themes folder
        $themesFolder = new Folder($this->config()->getThemesPath());

        // Get installation info
        $info = include $this->config()->getPath('/installation/info.php');

        // Install each theme package file
        foreach ($info['themes'] as $folderName) {
            $folder = $themesFolder->findFolders($folderName)->first();
            if (!empty($folder)) {
                try {
                    // Create and validate theme
                    $info = include $folder->getPath('info.php');
                    $theme = ThemeModel::create($info);

                    // Save theme
                    if ($theme && $theme->validate() && $theme->save()) {
                        $this->logger()->info($theme->name.' successfully installed.');
                    } else {
                        $this->logger()->error('Installing theme '.$theme->name.' failed for unknown reasons.');
                    }
                } catch (Throwable $ex) {
                    $this->logger()->error('Theme installation for '.$folder->getName().' failed.', ['Exception message' => $ex->getMessage()]);
                }
            } else {
                $this->logger()->warning('Theme folder '.$folderName.' not found.');
            }
        }

        // Update frontend theme
        SettingModel::updateById(['theme_id' => 2], 1)->save();

        return $this;
    }

    /**
     * Update settings with initial configuration.
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function updateSettings(): self
    {
        // Fetch and set settings
        $settings = SettingModel::findById(1);
        if ($settings) {
            $this->app()->set('settings', $settings);

            // Reset translator (to get correct language detection after database installation)
            $this->app()->set('translator', new Translator());

            // Update settings
            $settings->timezone = date_default_timezone_get();
            $settings->session_name = ini_get('session.name');
            $settings->session_lifetime = (int) ini_get('session.gc_maxlifetime');

            if (APP_MODE === 'DEV') {
                $settings->show_error_details = true;
                $settings->show_debugbar = true;
            }

            // Get language
            $language = $this->translator()->getCurrentLanguage();

            // Update language settings
            $settings->default_language_id = $language->id();

            $settings->save();
            $settings->setReadOnly();

            // Overwrite config with CMS settings
            $settings->overwriteConfig();

            return $this;
        }
        throw new RuntimeException('Settings not found.');
    }

    /**
     * Recreate config file.
     *
     * @param array $config
     *
     * @return self
     */
    public function createConfigFile(array $config): self
    {
        $this->config()->get('app')->set('url', $config['url']);

        $this->config()->get('database')->setData($config['database']);

        if (APP_MODE === 'DEV') {
            $this->config()->get('logger')->set('level', 'DEBUG');

            // Reset logger (to get correct log leve)
            $this->app()->set('logger', new Logger());
        }

        $this->config()->saveAsFile();

        return $this;
    }

    /**
     * Check whether installation is running (based on URL path).
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $urlPath = $this->request()->getUrlPath();

        return in_array($urlPath, [
            '/install',
            '/install/database',
            '/install/database/create',
            '/install/website',
            '/install/website/create',
            '/install/administrator',
            '/install/administrator/create',
            '/install/success',
        ]);
    }

    /**
     * Check whether database is already created.
     *
     * @return bool
     */
    public function databaseStatus(): bool
    {
        return $this->config()->get('database')->get('host') && $this->app()->get('database') && SettingModel::findById(1);
    }

    /**
     * Check whether settings are already created.
     *
     * @return bool
     */
    public function settingStatus(): bool
    {
        return $this->app()->get('database') && '' !== $this->settings()->website_title && '' !== $this->settings()->emailaddress;
    }

    /**
     * Check whether administrator user is already created.
     *
     * @return bool
     */
    public function administratorStatus(): bool
    {
        if ($this->databaseStatus()) {
            return (bool) UserModel::findById(1);
        }

        return false;
    }
}
