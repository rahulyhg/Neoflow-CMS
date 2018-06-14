<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\App;
use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\Core\AbstractModel as FrameworkAbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use ZipArchive;
use function translate;

abstract class AbstractExtensionModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $infoFileName = 'info.php';

    /**
     * @var array
     */
    protected $package;

    /**
     * Delete extension.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if (parent::delete()) {
            if (is_dir($this->getPath())) {
                $folder = new Folder($this->getPath());
                $folder->delete();
            }

            // Delete system config cache
            $this->cache()->deleteByTag('cms_core');
            $this->cache()->deleteByTag('cms_extensions');

            return true;
        }

        return false;
    }

    /**
     * Fetch info data from information file.
     *
     * @param string $file Info file path
     *
     * @return array
     *
     * @throws ValidationException
     */
    protected static function fetchInfoFile(string $file): array
    {
        if (is_file($file)) {
            $info = include $file;

            if (is_array($info)) {
                return $info;
            }
            throw new ValidationException(translate('Information file "{0}" is invalid', ['info.php']));
        }
        throw new ValidationException(translate('Information file "{0}" not found', ['info.php']));
    }

    /**
     * Load classes and functions.
     *
     * @return self
     */
    public function loadClassesAndFunctions(): self
    {
        // Add class directory to loader
        $classPath = $this->getPath('/classes');
        if (is_dir($classPath)) {
            $this->app()->get('loader')->addClassDirectory($classPath);
        }

        // Load functions from directory
        $functionPath = $this->getPath('/functions');
        if (is_dir($functionPath)) {
            $this->app()->get('loader')->loadFunctionsFromDirectory($functionPath);
        }

        return $this;
    }

    /**
     * Validate extension.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator->maxLength(100)->set('author', 'Author');

        $validator->maxLength(100)->set('copyright', 'Copyright');

        $validator->maxLength(250)->set('description', 'Description');

        $validator->maxLength(50)->set('version', 'Version');

        $validator->maxLength(100)->set('license', 'License');

        return (bool) $validator->validate();
    }

    /**
     * Reload the informatione of the extension.
     *
     * @return bool
     */
    public function reload(): bool
    {
        // Get info file path
        $infoFilePath = $this->getPath('info.php');

        // Fetch and set info data
        $info = $this->fetchInfoFile($infoFilePath);
        $this->setData($info);

        return $this->validate() && $this->save();
    }

    /**
     * Unpack extension package.
     *
     * @param File $file Extension package (zip archive)
     *
     * @return Folder
     *
     * @throws ValidationException
     */
    protected static function unpackPackage(File $file): Folder
    {
        // Create temporary update folder
        $folderPath = App::instance()->get('config')->getTempPath('/extension_'.uniqid());

        // Extract package
        $zipFile = new ZipArchive();
        if (true === $zipFile->open($file->getPath())) {
            $zipFile->extractTo($folderPath);
            $zipFile->close();

            // Delete update package
            $file->delete();

            return Folder::create($folderPath);
        }
        throw new ValidationException(translate('Zip archive "{0}" is invalid', [$file->getName()]));
    }

    /**
     * Install package (zip archive) and create extension.
     *
     * @param File $file Extension package (zip archive)
     *
     * @return self
     *
     * @throws ValidationException
     */
    public static function installPackage(File $file): self
    {
        $folder = static::unpackPackage($file);

        try {
            // Get info file path
            $infoFilePath = $folder->getPath('info.php');

            // Fetch info
            $info = static::fetchInfoFile($infoFilePath);

            // Create extension
            $extension = static::create($info);

            // Check if the folder already exist
            if (!is_dir($extension->getPath())) {
                // Validate info data
                $extension->validate();

                // Copy folder to
                $folder->copy($extension->getPath());

                $extension->save();

                return $extension;
            }
            throw new ValidationException(translate('Folder name "{0}" is already in use', [$extension->folder_name]));
        } finally {
            // Delete extension folder
            $folder->delete();
        }
    }

    /**
     * Install extension update package (zip archive).
     *
     * @param File $file Extension update package
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function installUpdatePackage(File $file): bool
    {
        $folder = self::unpackPackage($file);

        try {
            // Get info file path
            $infoFilePath = $folder->getPath('info.php');

            // Fetch info
            $info = self::fetchInfoFile($infoFilePath);

            // Check if same identifier
            if (isset($info['identifier']) && $this->identifier === $info['identifier']) {
                // Check if extension is up to date
                if (isset($info['version']) && $this->version === $info['version']) {
                    throw new ValidationException(translate('The extension "{0}" is already up to date', [$this->name]));
                }

                // Check if supported version for update
                if (isset($info['for']) && is_array($info['for']) && in_array($this->version, $info['for'])) {
                    // Delete current installed folder
                    Folder::unlink($this->getPath(), true);

                    $this->setData($info);

                    // Validate info data
                    $this->validate();

                    // Copy folder
                    $folder->copy($this->getPath());

                    return $this->save();
                }
                throw new ValidationException(translate('The version {0} of the extension "{1}" is not supported', [
                        $this->version,
                        $this->name,
                    ]));
            }
            throw new ValidationException(translate('The extension "{0}" is not compatible', [$this->name]));
        } finally {
            // Delete extension folder
            $folder->delete();
        }
    }

    /**
     * Save extension.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        // Delete cms cache first
        $this->cache()->deleteByTag('cms_extensions');
        $this->cache()->deleteByTag('cms_core');

        return parent::save($preventCacheClearing);
    }

    /**
     * Toggle activation.
     *
     * @return self
     */
    public function toggleActivation(): self
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }

        return $this;
    }

    /**
     * Set extension value.
     *
     * @param string $property Extension property
     * @param mixed  $value    Property value
     * @param bool   $silent   Set TRUE to prevent the tracking of the change
     *
     * @return self
     *
     * @throws RuntimeException
     */
    public function set(string $property, $value = null, bool $silent = false): FrameworkAbstractModel
    {
        if ('version' === $property && $this->version !== $value) {
            $this->oldVersion = $this->version;
        }

        return parent::set($property, $value, $silent);
    }

    /**
     * Get extension path.
     *
     * @param string $additionalPath Additional path
     *
     * @return string
     */
    abstract public function getPath(string $additionalPath = ''): string;
}
