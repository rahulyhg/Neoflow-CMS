<?php

namespace Neoflow\Framework\Persistence\Caching;

class FileCache extends AbstractCache
{
    /**
     * @var string
     */
    protected $fileCacheFolder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fileCacheFolder = $this->config()->getTempPath(DIRECTORY_SEPARATOR.'cache');
        if (!is_dir($this->fileCacheFolder)) {
            mkdir($this->fileCacheFolder);
        }

        parent::__construct();
    }

    /**
     * Get file name.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getFileName(string $key): string
    {
        return $this->fileCacheFolder.DIRECTORY_SEPARATOR.'cache_'.sha1($key);
    }

    /**
     * Fetch cache value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function fetch(string $key)
    {
        $cacheFile = $this->getFileName($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $data = file_get_contents($cacheFile);
        $data = unserialize($data);
        // Delete file if unserializing didn't work or cache is expired
        if (2 !== count($data) || time() > $data[0]) {
            unlink($cacheFile);

            return false;
        }

        return $data[1];
    }

    /**
     * Store cache value.
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $ttl
     * @param array  $tags
     *
     * @throws RuntimeException
     *
     * @return bool
     */
    public function store(string $key, $data, int $ttl = 0, array $tags = []): bool
    {
        // Set key to tags
        $this->setKeyToTags($tags, $key);
        // Opening the file in read/write mode
        $cacheFile = $this->getFileName($key);
        $handle = fopen($cacheFile, 'w+');
        if (!$handle) {
            throw new RuntimeException('Cache file "'.$cacheFile.'" could not be opened');
        }

        if (0 === $ttl) {
            $ttl = 31536000 * 10; // 10 years, like infinite :)
        }
        // Serializing data and TTL
        fwrite($handle, serialize([time() + $ttl, $data]));

        return fclose($handle);
    }

    /**
     * Delete cache value.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        $cacheFile = $this->getFileName($key);
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }

        return false;
    }

    /**
     * Check whether cache value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return (bool) $this->fetch($key);
    }

    /**
     * Clear complete cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        $this->clearTags();

        $cacheFiles = scandir($this->fileCacheFolder);
        foreach ($cacheFiles as $cacheFile) {
            $cacheFile = $this->fileCacheFolder.DIRECTORY_SEPARATOR.$cacheFile;
            if (is_file($cacheFile) && 0 === mb_strpos(basename($cacheFile), 'cache_')) {
                unlink($cacheFile);
            }
        }

        return true;
    }
}
