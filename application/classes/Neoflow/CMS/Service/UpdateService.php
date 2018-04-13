<?php
namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\CMS\UpdateManager;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;
use Throwable;
use ZipArchive;

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
        $updateFolderPath = $this->config()->getTempPath('/update_' . uniqid());
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
        throw new ValidationException(translate('Zip archive ({0}) is invalid', [$updatePackageFile->getName()]));
    }

    /**
     * Fetch info data from information file.
     *
     * @param Folder $updateFolder Update folder (unpacked update package)
     *
     * @return array
     *
     * @throws ValidationException
     */
    protected function fetchInfo(Folder $updateFolder): array
    {
        $infoFilePath = $updateFolder->getPath('info.php');
        if (is_file($infoFilePath)) {
            $info = include $infoFilePath;

            if (isset($info['version']) && isset($info['for']) && isset($info['sql']) && isset($info['files'])) {
                return $info;
            }
            throw new ValidationException(translate('Information file ({0}) is invalid', ['info.php']));
        }
        throw new ValidationException(translate('Information file ({0}) not found', ['info.php']));
    }

    /**
     * Validate version compatibility.
     *
     * @param array $info Info data
     *
     * @return bool
     *
     * @throws ValidationException
     */
    protected function validateVersion(array $info)
    {
        if ($info['version'] === $this->config()->get('app')->get('version')) {
            throw new ValidationException(translate('The CMS is already up to date'));
        }

        foreach ($info['for'] as $supportedVersion) {
            if ($supportedVersion === $this->config()->get('app')->get('version')) {
                return true;
            }
        }

        throw new ValidationException(translate('The version ({0}) of the CMS is not supported', [$this->config()->get('app')->get('version')]));
    }

    /**
     * Install update package.
     *
     * @param File $updatePackageFile Update package (Zip archive)
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function install(File $updatePackageFile): bool
    {
        $updateFolder = $this->unpack($updatePackageFile);

        try {
            // Fetch info data
            $info = $this->fetchInfo($updateFolder);

            // Check and validate version support
            $this->validateVersion($info);

            // Add class directory to loader
            $classPath = $updateFolder->getPath('/classes');
            if (is_dir($classPath)) {
                $this->app()->get('loader')->addClassDirectory($classPath);
            }

            if (class_exists('\\Neoflow\\CMS\\UpdateManager')) {
                $manager = new UpdateManager($updateFolder, $info);
                $manager->install();
            }

            $this->updateModules($info, $updateFolder);
            $this->updateThemes($info, $updateFolder);
        } finally {
            // Delete update folder
            $updateFolder->delete();
        }

        return true;
    }

    /**
     * Update modules.
     *
     * @param array  $info         Info data
     * @param Folder $updateFolder Update folder
     *
     * @return bool
     */
    protected function updateModules(array $info, Folder $updateFolder): bool
    {
        if (isset($info['modules'])) {
            foreach ($info['modules'] as $identifier => $packageName) {
                $files = $updateFolder->findFiles('modules/' . $packageName);
                foreach ($files as $file) {
                    try {
                        $module = ModuleModel::findByColumn('identifier', $identifier);
                        if ($module) {
                            $module->installUpdate($file);
                        } else {
                            $module = new ModuleModel();
                            $module->install($file);
                        }
                    } catch (Throwable $ex) {
                        $this->logger()->warning('Module update installation for ' . $packageName . ' failed.', [
                            'Exception message' => $ex->getMessage(),
                        ]);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Update themes.
     *
     * @param array  $info         Info data
     * @param Folder $updateFolder Update folder
     *
     * @return bool
     */
    protected function updateThemes(array $info, Folder $updateFolder): bool
    {
        if (isset($info['themes'])) {
            foreach ($info['themes'] as $identifier => $packageName) {
                $theme = ThemeModel::findByColumn('themes', $identifier);
                $files = $updateFolder->findFiles('themes/' . $packageName);
                foreach ($files as $file) {
                    try {
                        $theme = ThemeModel::findByColumn('identifier', $identifier);
                        if ($theme) {
                            $theme->installUpdate($file);
                        } else {
                            $theme = new ThemeModel();
                            $theme->install($file);
                        }
                    } catch (Throwable $ex) {
                        $this->logger()->warning('Theme update installation for ' . $packageName . ' failed.', [
                            'Exception message' => $ex->getMessage(),
                        ]);
                    }
                }
            }

            return true;
        }

        return false;
    }
}
