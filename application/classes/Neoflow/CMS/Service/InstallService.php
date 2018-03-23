<?php
namespace Neoflow\CMS\Service;

use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Filesystem\File;
use Neoflow\CMS\Core\AbstractService;
use Neoflow\Framework\Handler\Config;
use Neoflow\Framework\Handler\Translator;
use Neoflow\Framework\Persistence\Database;
use const ROOT_DIR;

class InstallService extends AbstractService
{

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
     * Create config file (uses template config file).
     *
     * @param array $config
     *
     * @return self
     */
    public function createConfigFile(array $config): self
    {
        // Get config content from installation config file
        $configFilePath = $this->config()->getPath('/installation/configTemplate.php');
        $configFileContent = file_get_contents($configFilePath);

        // Replace placeholder with values
        $configFileContent = str_replace('[url]', $config['url'], $configFileContent);
        foreach ($config['database'] as $key => $value) {
            $configFileContent = str_replace('[' . $key . ']', $value, $configFileContent);
        }

        // Create config file
        $configFile = File::create(ROOT_DIR . '/config.php', $configFileContent, true);

        // Create additional config data
        $additionalConfigData = array_merge($this->config()->toArray(), $config);

        $this->app()->set('config', Config::createConfigByFile($configFile->getPath(), $additionalConfigData));

        return $this;
    }

    /**
     * Check whether database is created
     * @return bool
     */
    public function databaseStatus(): bool
    {
        return ($this->app()->get('database') && SettingModel::findById(1));
    }

    /**
     * Check whether settings are created
     * @return bool
     */
    public function settingStatus(): bool
    {
        return ($this->settings()->website_title !== '' && $this->settings()->sender_emailaddress !== '');
    }

    /**
     * Check whether administrator user is created
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
