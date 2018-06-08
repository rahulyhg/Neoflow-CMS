<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\UpdateManager;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use ZipArchive;
use function translate;

class UpdateService extends AbstractService
{
    /**
     * Unpack update package.
     *
     * @param File $updatePackageFile Update package file (Zip archive)
     * @param bool $delete            Set FALSE to disable deleting the update package after unpacking
     *
     * @return Folder
     *
     * @throws ValidationException
     */
    protected function unpack(File $updatePackageFile, bool $delete = true): Folder
    {
        // Create temporary update folder
        $updateFolderPath = $this->config()->getPath('/update_'.uniqid());
        $updateFolder = Folder::create($updateFolderPath);

        // Extract package
        $zipFile = new ZipArchive();
        if (true === $zipFile->open($updatePackageFile->getPath())) {
            $zipFile->extractTo($updateFolderPath);
            $zipFile->close();

            // Delete update package
            if ($delete) {
                $updatePackageFile->delete();
            }

            return $updateFolder;
        }
        throw new ValidationException(translate('Zip archive "{0}" is invalid', [$updatePackageFile->getName()]));
    }

    /**
     * Install CMS update.
     *
     * @param File $updatePackageFile Update package (Zip archive)
     *
     * @return bool
     */
    public function installUpdate(File $updatePackageFile): bool
    {
        // Unpack update package and create update folder
        $updateFolder = $this->unpack($updatePackageFile);

        // Create update manager
        $manager = $this->createManager($updateFolder);

        // Install CMS update
        return $manager->installUpdate();
    }

    /**
     * Install modules and themes updates.
     *
     * @param string $updateFolderPath Update folder path
     *
     * @return bool
     */
    public function installExtensionUpdates($updateFolderPath): bool
    {
        // Create update folder
        $updateFolder = Folder::load($updateFolderPath);

        // Create update manager
        $manager = $this->createManager($updateFolder);

        // Install extension updates
        return $manager->installExtensionUpdates();
    }

    /**
     * Create update manager.
     *
     * @param Folder $updateFolder Update folder
     *
     * @return UpdateManager
     */
    protected function createManager(Folder $updateFolder): UpdateManager
    {
        // Add class directory to loader
        $classPath = $updateFolder->getPath('/classes');
        if (is_dir($classPath)) {
            $this->app()->get('loader')->addClassDirectory($classPath);
        }

        if (class_exists('Neoflow\\CMS\\UpdateManager')) {
            return new UpdateManager($updateFolder);
        }

        throw new RuntimeException('Update manager not found');
    }
}
