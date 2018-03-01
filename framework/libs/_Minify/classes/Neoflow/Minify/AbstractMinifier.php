<?php

namespace Neoflow\Minify;

use InvalidArgumentException;

abstract class AbstractMinifier
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Add code.
     *
     * @param string $code
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function addCode($code)
    {
        if (is_string($code)) {
            $this->code .= $code.PHP_EOL.PHP_EOL;

            return $this;
        }
        throw InvalidArgumentException('Code is not a string');
    }

    /**
     * Add path or URL of resource.
     *
     * @param string $path
     *
     * @return self
     */
    public function add($path)
    {
        if (is_array($path)) {
            array_filter($path, function ($path) {
                $this->add($path);
            });
        } elseif (filter_var($path, FILTER_VALIDATE_URL) || is_dir($path)) {
            $code = file_get_contents($path);
            if ($code) {
                $this->addCode($code);
            }
        }

        return $this;
    }

    abstract public function minify($targetPath = null);

    /**
     * Save code to file.
     *
     * @param string $targetFilePath
     *
     * @return int
     */
    protected function saveToFile($targetFilePath)
    {
        if (is_string($targetFilePath)) {
            return file_put_contents($targetFilePath, $this->code);
        }

        return false;
    }
}
