<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\CMS\UpdateManager;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;
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
        $updateFolderPath = $this->config()->getTempPath('/update_'.uniqid());
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

            if (isset($info['version']) && isset($info['for']) && isset($info['path']['sql']) && isset($info['path']['files']) && isset($info['path']['packages'])) {
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
    protected function validateVersion(array $info): bool
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
     * Old install method (only needed for update from 1.0.0-a1 to 1.0.0-a2).
     *
     * @param File $updatePackageFile Update package (Zip archive)
     *
     * @throws ValidationException
     */
    public function install(File $updatePackageFile)
    {
        $this->start($updatePackageFile);
    }

    /**
     * Start update.
     *
     * @param File $updatePackageFile Update package (Zip archive)
     *
     * @throws ValidationException
     */
    public function start(File $updatePackageFile)
    {
        // Unpack update zip package
        $updateFolder = $this->unpack($updatePackageFile);

        // Fetch info data
        $info = $this->fetchInfo($updateFolder);

        // Check and validate version support
        $this->validateVersion($info);

        return $this->sendUpdateRequest([
                'update' => 1,
                'folder' => $updateFolder->getName(),
        ]);
    }

    /**
     * Execute update listener.
     *
     * @return bool
     */
    public function execute(): bool
    {
        $updateStep = (int) $this->request()->getGet('update');
        $updateFolderName = (string) $this->request()->getGet('folder');

        if ($updateFolderName) {
            $updateFolderPath = $this->config()->getTempPath('/'.sanitize_file_name($updateFolderName));

            if (is_dir($updateFolderPath)) {
                if (1 === $updateStep) {
                    return $this->installUpdate($updateFolderPath);
                } elseif (2 === $updateStep) {
                    return $this->installExtensionUpdates($updateFolderPath);
                } elseif (3 === $updateStep) {
                    return Folder::unlink($updateFolderPath);
                }
            }
        }

        return false;
    }

    /**
     * Update files and database (step 1).
     *
     * @param array $updateFolderPath Update folder path
     *
     * @return bool
     */
    protected function updateFilesAndDatabase(string $updateFolderPath): bool
    {
        // Create update folder
        $updateFolder = new Folder($updateFolderPath);

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
            if ($manager->updateDatabase() && $manager->updateFiles() && $manager->updateConfig()) {
                return $this->sendUpdateRequest([
                        'update' => 2,
                        'folder' => $updateFolder->getName(),
                ]);
            }
        }

        return false;
    }

    /**
     * Update extensions (step 2).
     *
     * @param array $updateFolderPath Update folder path
     *
     * @return bool
     */
    protected function updateExtensions(string $updateFolderPath): bool
    {
        // Create update folder
        $updateFolder = new Folder($updateFolderPath);

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
            if ($manager->updateModules() && $manager->updateThemes()) {
                return $this->sendUpdateRequest([
                        'update' => 3,
                        'folder' => $updateFolder->getName(),
                ]);
            }
        }

        return false;
    }

    /**
     * Send update requests.
     *
     * @param array $params Update parameters
     *
     * @return bool
     */
    protected function sendUpdateRequest(array $params): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->request()->getUrl(false).'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        return (bool) $result;
    }
}
