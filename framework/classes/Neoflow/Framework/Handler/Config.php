<?php

namespace Neoflow\Framework\Handler;

use Neoflow\Framework\Common\Container;

class Config extends Container
{
    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * Get url.
     *
     * @param string $additionalUrlPath
     *
     * @return string
     */
    public function getUrl(string $additionalUrlPath = ''): string
    {
        return normalize_url($this->get('url').'/'.$additionalUrlPath);
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
    public static function createConfigByFile(string $configFilePath, array $additionalConfigData = []): self
    {
        if (is_file($configFilePath)) {
            $configData = array_merge(include $configFilePath, $additionalConfigData);

            return new self($configData, false, true);
        }
        throw new RuntimeException('Config file not found (path: '.$configFilePath.')');
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
        return normalize_path($this->get('path').DIRECTORY_SEPARATOR.$additionalPath);
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

        return $this->getPath($path.'/'.$additionalPath);
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

        return $this->getPath($path.'/'.$additionalPath);
    }
}
