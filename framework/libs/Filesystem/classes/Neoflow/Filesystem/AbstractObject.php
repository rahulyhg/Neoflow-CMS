<?php
namespace Neoflow\Filesystem;

use ReflectionClass;

abstract class AbstractObject
{

    /**
     * Filesystem object path.
     *
     * @var string
     */
    protected $path;

    /**
     * Load filesystem object.
     */
    public static function load(string $path)
    {
        return new static($path);
    }

    /**
     * Get type of filesystem object.
     *
     * @return string
     */
    public function getType(): string
    {
        $reflect = new ReflectionClass($this);

        return strtolower($reflect->getShortName());
    }

    /**
     * Check wether filesystem object is a file.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        return 'file' === $this->getType();
    }

    /**
     * Check wether filesystem object is a folder.
     *
     * @return bool
     */
    public function isFolder(): bool
    {
        return 'folder' === $this->getType();
    }
}
