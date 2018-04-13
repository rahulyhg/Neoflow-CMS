<?php
namespace Neoflow\Framework\Handler;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\Common\Container;
use RuntimeException;

class Config extends Container
{

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Get url.
     *
     * @param string $additionalUrlPath
     *
     * @return string
     */
    public function getUrl(string $additionalUrlPath = ''): string
    {
        return normalize_url($this->get('app')->get('url') . '/' . $additionalUrlPath);
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
    public static function createByFile(string $configFilePath, array $additionalConfigData = []): self
    {
        if (is_file($configFilePath)) {
            $configData = require $configFilePath;

            // Set application url when not already set
            if (!$configData['app']['url']) {
                $configData['app']['url'] = normalize_url(request_url(false, false) . base_path(APP_PATH));
            }

            // Set absolute application path
            $configData['app']['path'] = APP_PATH;

            // Merge final config data
            $mergedConfigData = array_merge_recursive($configData, $additionalConfigData);

            // Create config
            return new static($mergedConfigData, false, true);
        }
        throw new RuntimeException('Config file not found.');
    }

    /**
     * Get path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getPath(string $additionalPath = ''): string
    {
        return normalize_path($this->get('app')->get('path') . DIRECTORY_SEPARATOR . $additionalPath);
    }

    /**
     * Get temp folder path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getTempPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('temp')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }

    /**
     * Get application folder path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getApplicationPath(string $additionalPath = ''): string
    {
        $path = $this->get('folders')->get('application')->get('path');

        return $this->getPath($path . '/' . $additionalPath);
    }
}
