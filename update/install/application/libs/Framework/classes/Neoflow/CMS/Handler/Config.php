<?php

namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\Filesystem\File;
use Neoflow\Framework\Handler\Config as FrameworkConfig;
use RuntimeException;

class Config extends FrameworkConfig
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Create config by file.
     *
     * @param string $configFilePath       File path of config
     * @param array  $additionalConfigData Additional config data
     *
     * @return static
     *
     * @throws RuntimeException
     */
    public static function createByFile(string $configFilePath, array $additionalConfigData = []): FrameworkConfig
    {
        if (!is_file($configFilePath)) {
            $configFilePath = APP_PATH.'/installation/config.php';
        }

        return parent::createByFile($configFilePath, $additionalConfigData);
    }

    /**
     * Get themes url.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getThemesUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('themes')->get('path');

        return $this->getUrl($path.'/'.$additionalUrlPath);
    }

    /**
     * Get modules url.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getModulesUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('modules')->get('path');

        return $this->getUrl($path.'/'.$additionalUrlPath);
    }

    /**
     * Get media url.
     *
     * @param string $additionalUrlPath Additional URL path
     *
     * @return string
     */
    public function getMediaUrl(string $additionalUrlPath = ''): string
    {
        $path = $this->get('folders')->get('media')->get('path');

        return $this->getUrl($path.'/'.$additionalUrlPath);
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

        return $this->getPath($path.'/'.$additionalPath);
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

        return $this->getPath($path.'/'.$additionalPath);
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

        return $this->getPath($path.'/'.$additionalPath);
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

        return $this->getPath($path.'/'.$additionalPath);
    }

    /**
     * Save and overwrite current config as file.
     *
     * @return bool
     */
    public function saveAsFile(): bool
    {
        $configData = $this->toArray();

        // Clean config from not need params
        unset($configData['session']);
        unset($configData['app']['path']);
        unset($configData['app']['email']);
        unset($configData['app']['languages']);
        unset($configData['app']['timezone']);

        // Create config file
        $phpFile = File::create(APP_PATH.'/config.php');

        // Create config content
        $content = '<?php'.PHP_EOL.'return '.array_export($configData);

        // Set config content to file
        return (bool) $phpFile->setContent($content);
    }
}
