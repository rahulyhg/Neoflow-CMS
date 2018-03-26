<?php
namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\Framework\Handler\Config as FrameworkConfig;

class Config extends FrameworkConfig
{

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Get themes url.
     *
     * @param string $additionalUrlPath
     *
     * @return string
     */
    public function getThemesUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('themes')->get('path');

        return $this->getUrl($path . '/' . $additionalUrlPath);
    }

    /**
     * Get modules url.
     *
     * @param string $additionalUrlPath
     *
     * @return string
     */
    public function getModulesUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('modules')->get('path');

        return $this->getUrl($path . '/' . $additionalUrlPath);
    }

    /**
     * Get media url.
     *
     * @param string $additionalUrlPath
     *
     * @return string
     */
    public function getMediaUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('media')->get('path');

        return $this->getUrl($path . '/' . $additionalUrlPath);
    }

    /**
     * Get themes path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getThemesPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('themes')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }

    /**
     * Get modules path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getModulesPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('modules')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }

    /**
     * Get media path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getMediaPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('media')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }

    /**
     * Get logs path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getLogsPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('logs')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }

    /**
     * Create config by file.
     *
     * @param string $configFilePath       File path of config
     * @param array  $additionalConfigData Additional config data
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public static function createConfigByFile(string $configFilePath, array $additionalConfigData = []): FrameworkConfig
    {
        // Get application config file as additional config data
        $applicationConfigFilePath = APP_ROOT . '/application/config.php';
        if (is_file($applicationConfigFilePath)) {
            $additionalConfigData = array_merge(require $applicationConfigFilePath, $additionalConfigData);
        }

        // Define config data
        if (is_file($configFilePath)) {
            $configData = require $configFilePath;
        } else {
            $configData = [
                'url' => normalize_url(request_url(false, false) . base_path(APP_ROOT)),
                'database' => [
                    'host' => '',
                    'dbname' => '',
                    'username' => '',
                    'password' => '',
                    'charset' => 'UTF8',
                ],
            ];
        }

        // Merge final config data
        $configData = array_merge($configData, $additionalConfigData);

        // Create config
        return new self($configData, false, true);
    }
}
