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
    public function addCode(string $code): self
    {
        if (is_string($code)) {
            $this->code .= $code . PHP_EOL . PHP_EOL;

            return $this;
        }
        throw InvalidArgumentException('Code is not a string');
    }

    /**
     * Add path or URL of resource.
     *
     * @param string $path Path or URL of ressource
     *
     * @return self
     */
    public function add(string $path): self
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

    abstract public function minify(): self;

    /**
     * Save minified code as file.
     *
     * @param string $filePath File path
     *
     * @return bool
     */
    protected function saveAsFile(string $filePath): bool
    {
        return (bool) file_put_contents($filePath, $this->code);
    }
}
