<?php
namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Handler\Translator;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\Persistence\Database;

class InstallService extends AbstractService
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Clear complete cache
        $this->cache()->clear();
    }

    /**
     * Etablish database connection, create tables and insert data.
     *
     * @param array $config Database configuration
     *
     * @return self
     */
    public function createDatabase(array $config): self
    {
        // Etablish connection to database
        $database = Database::connect($config['host'], $config['dbname'], $config['username'], $config['password'], $config['charset']);
        $this->app()->set('database', $database);

        // Create tables
        $createSqlFilePath = $this->config()->getPath('/installation/tables.sql');
        $this->database()->executeFile($createSqlFilePath);

        // Get SQL to insert data into tables
        $insertSqlFilePath = $this->config()->getPath('/installation/data.sql');
        $this->database()->executeFile($insertSqlFilePath);

        return $this;
    }

    /**
     * Update settings with initial configuration.
     *
     * @return self
     */
    public function updateSettings(): self
    {
        // Presetting
        $settings = SettingModel::findById(1);
        $this->app()->set('settings', $settings);

        // Reset translator (to get correct language detection after database installation)
        $this->app()->set('translator', new Translator());

        // Get language
        $language = $this->translator()->getActiveLanguage();

        // Update settings with config params
        $settings->timezone = $this->config()->get('timezone');
        $settings->session_name = $this->config()->get('session')->get('name');
        $settings->session_lifetime = $this->config()->get('session')->get('lifetime');
        $settings->default_language_id = $language->id();
        $settings->language_ids = [
            $language->id(),
        ];
        $settings->save();
        $settings->setReadOnly();

        // Update configured langauges
        $this->config()->set('languages', [$language->code]);

        $this->app()->set('settings', $settings);

        return $this;
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
        return $this->app()->get('database') && '' !== $this->settings()->website_title && '' !== $this->settings()->sender_emailaddress;
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
