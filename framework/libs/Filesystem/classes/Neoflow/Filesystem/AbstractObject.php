<?php

namespace Neoflow\Filesystem;

abstract class AbstractObject
{
    /**
     * Filesystem object path.
     *
     * @var string
     */
    protected $path;

    /**
     * Filesystem object type (file or folder).
     *
     * @var string
     */
    protected $objectType;

    /**
     * Load filesystem object.
     *
     * @param string $path Path of filesystem object
     *
     * @return self
     */
    public static function load(string $path): self
    {
        return new static($path);
    }

    /**
     * Check wether filesystem object is a file.
     *
     * @return bool
     */
    public function isFile(): bool
    {
        return 'file' === $this->objectType;
    }

    /**
     * Check wether filesystem object is a folder.
     *
     * @return bool
     */
    public function isFolder(): bool
    {
        return 'folder' === $this->objectType;
    }
}
