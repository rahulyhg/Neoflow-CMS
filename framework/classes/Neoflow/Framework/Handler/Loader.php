<?php
namespace Neoflow\Framework\Handler;

class Loader
{

    /**
     * @var array
     */
    protected $classDirectories = [];

    /**
     * Load classes and functions of libraries from base directory paths.
     *
     * @param array $basePaths Library base directory paths
     *
     * @return self
     */
    public function loadLibraries(array $basePaths)
    {
        foreach ($basePaths as $basePath) {
            $paths = array_filter(glob($basePath . '/[!_]*'), 'is_dir');

            foreach ($paths as $path) {
                $this
                    ->loadFunctionsFromDirectory($path . '/functions')
                    ->addClassDirectory($path . '/classes');
            }
        }

        return $this;
    }

    /**
     * Load functions from directories.
     *
     * @param array $functionDirectories
     *
     * @return self
     */
    public function loadFunctionsFromDirectories(array $functionDirectories)
    {
        foreach ($functionDirectories as $functionDirectory) {
            $this->loadFunctionsFromDirectory($functionDirectory);
        }

        return $this;
    }

    /**
     * Load functions from directory.
     *
     * @param string $functionDirectory
     *
     * @return self
     */
    public function loadFunctionsFromDirectory($functionDirectory)
    {
        foreach (glob($functionDirectory . '/*.php') as $functionFilePath) {
            require_once $functionFilePath;
        }

        return $this;
    }

    /**
     * Load class file.
     *
     * @param string $className Name of class
     *
     * @return bool
     */
    public function loadClassFile($className)
    {
        foreach ($this->classDirectories as $classDirectory) {
            $classFilePath = normalize_path($classDirectory . DIRECTORY_SEPARATOR . $className . '.php');
            if (is_file($classFilePath)) {
                require_once $classFilePath;

                return true;
            }
        }

        return false;
    }

    /**
     * Register autoload.
     *
     * @return bool
     */
    public function registerAutoload()
    {
        return spl_autoload_register([$this, 'loadClassFile']);
    }

    /**
     * Add directory.
     *
     * @param string $classDirectory
     *
     * @return self
     */
    public function addClassDirectory($classDirectory)
    {
        $this->classDirectories[] = $classDirectory;

        return $this;
    }

    /**
     * Add directories.
     *
     * @param array $classDirectories
     *
     * @return self
     */
    public function addClassDirectories($classDirectories)
    {
        $this->classDirectories = array_merge($this->classDirectories, $classDirectories);

        return $this;
    }

    /**
     * Constrcutor.
     *
     * @param array $functionDirectories
     * @param array $classDirectories
     * @param bool  $registerAutoload
     */
    public function __construct(array $functionDirectories = [], array $classDirectories = [], $registerAutoload = true)
    {
        // Load functions from directories
        $this->loadFunctionsFromDirectories($functionDirectories);

        // Add class directories
        $this->addClassDirectories($classDirectories);

        // Register autoload
        if ($registerAutoload) {
            $this->registerAutoload();
        }
    }
}
