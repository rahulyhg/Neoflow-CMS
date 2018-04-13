<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\Core\AbstractModel as FW_AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Validation\ValidationException;
use ZipArchive;

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
    public function delete()
    {
        if (parent::delete()) {
            if (is_dir($this->getPath())) {
                $folder = new Folder($this->getPath());
                $folder->delete();
            }

            // Delete system config cache
            $this->cache()->deleteByTag('system-configurations');

            return true;
        }

        return false;
    }

    /**
     * Fetch info data from information file.
     *
     * @params string $infoFilePath
     *
     * @return array
     *
     * @throws ValidationException
     */
    protected function fetchInfoFile(string $infoFilePath): array
    {
        if (is_file($infoFilePath)) {
            $info = include $infoFilePath;

            if (is_array($info)) {
                return $info;
            }
            throw new ValidationException(translate('Information file ({0}) is invalid', ['info.php']));
        }
        throw new ValidationException(translate('Information file ({0}) not found', ['info.php']));
    }

    /**
     * Validate extension.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->maxLength(100)
            ->set('author', 'Author');

        $validator
            ->maxLength(100)
            ->set('copyright', 'Copyright');

        $validator
            ->maxLength(250)
            ->set('description', 'Description');

        $validator
            ->maxLength(50)
            ->set('version', 'Version');

        $validator
            ->maxLength(100)
            ->set('license', 'License');

        return (bool) $validator->validate();
    }

    /**
     * Reload the informatione of the extension.
     *
     * @return bool
     */
    public function reload()
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
     * @param File $extensionPackageFile Extension package file (Zip archive)
     * @param bool $delete               Set FALSE to disable deleting the extension package after unpacking
     *
     * @return Folder
     *
     * @throws ValidationException
     */
    protected function unpack(File $extensionPackageFile, bool $delete = true): Folder
    {
        // Create temporary update folder
        $extensionFolderPath = $this->config()->getTempPath('/extension_'.uniqid());
        $extensionFolder = Folder::create($extensionFolderPath);

        // Extract package
        $zipFile = new ZipArchive();
        if (true === $zipFile->open($extensionPackageFile->getPath())) {
            $zipFile->extractTo($extensionFolderPath);
            $zipFile->close();

            // Delete update package
            if ($delete) {
                $extensionPackageFile->delete();
            }

            return $extensionFolder;
        }
        throw new ValidationException(translate('Zip archive ({0}) is invalid', [$extensionPackageFile->getName()]));
    }

    /**
     * Install extension package.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function install(File $extensionPackageFile): bool
    {
        $installFolder = $this->unpack($extensionPackageFile);

        try {
            // Get info file path
            $infoFilePath = $installFolder->getPath('info.php');

            // Fetch and set info data
            $info = $this->fetchInfoFile($infoFilePath);
            $this->setData($info);

            // Check if the folder already exist
            if (!is_dir($this->getPath())) {
                // Validate info data
                $this->validate();

                // Copy folder to
                $installFolder->copy($this->getPath());

                // Add class directory to loader
                $classPath = $this->getPath('/classes');
                if (is_dir($classPath)) {
                    $this->app()->get('loader')->addClassDirectory($classPath);
                }

                return $this->save();
            }
            throw new ValidationException(translate('Folder name ({0}) is already in use', [$this->folder_name]));
        } finally {
            // Delete extension folder
            $installFolder->delete();
        }
    }

    /**
     * Install update extension package.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function installUpdate(File $extensionPackageFile): bool
    {
        $updateFolder = $this->unpack($extensionPackageFile);

        try {
            // Get info file path
            $infoFilePath = $updateFolder->getPath('info.php');

            // Fetch and set info data
            $info = $this->fetchInfoFile($infoFilePath);

            // Check if same identifier
            if (isset($info['identifier']) && $this->identifier === $info['identifier']) {
                // Check if extension is up to date
                if (isset($info['version']) && $this->version === $info['version']) {
                    throw new ValidationException(translate('The extension ({0}) is already up to date', [$this->name]));
                }

                // Check if supported version for update
                if (isset($info['for']) && is_array($info['for']) && in_array($this->version, $info['for'])) {
                    // Delete current installed folder
                    Folder::unlink($this->getPath(), true);

                    $this->setData($info);

                    // Validate info data
                    $this->validate();

                    // Copy folder
                    $updateFolder->copy($this->getPath());

                    // Add class directory to loader
                    $classPath = $this->getPath('/classes');
                    if (is_dir($classPath)) {
                        $this->app()->get('loader')->addClassDirectory($classPath);
                    }

                    return $this->save();
                }
                throw new ValidationException(translate('The version ({0}) of the extension ({1}) is not supported', [$this->version, $this->name]));
            }
            throw new ValidationException(translate('The extension ({0}) is not compatible', [$this->name]));
        } finally {
            // Delete extension folder
            $updateFolder->delete();
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
        $this->cache()->deleteByTag('system-configurations');

        return parent::save($preventCacheClearing);
    }

    /**
     * Toggle activation.
     *
     * @return self
     */
    public function toggleActivation()
    {
        if ($this->is_active) {
            $this->is_active = false;
        } else {
            $this->is_active = true;
        }

        return $this;
    }

    /**
     * Set extension entity value.
     *
     * @param string $key    Key of entity value
     * @param mixed  $value  Entity value
     * @param bool   $silent State if setting shouldn't be tracked
     *
     * @return self
     */
    protected function set($key, $value = null, $silent = false): FW_AbstractModel
    {
        if ('version' === $key && $this->version !== $value) {
            $this->oldVersion = $this->version;
        }

        return parent::set($key, $value, $silent);
    }

    /**
     * Get extension path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    abstract public function getPath($additionalPath = '');
}
