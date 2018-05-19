<?php

namespace Neoflow\CMS;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;
use Throwable;
use function generate_url;
use function translate;

class UpdateManager {

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var string
     */
    protected $version;
    protected $newVersion;

    /**
     * Constructor.
     *
     * @param Folder $folder Update folder
     * @param array  $info   Old info data (only needed for update from 1.0.0-a1 to 1.0.0-a2).
     */
    public function __construct(Folder $folder, array $info = [])
    {
        $this->folder = $folder;

        $this->info = $this->fetchInfo();
        $this->version = $this->config()->get('app')->get('version');
        $this->newVersion = $this->info['version'];
    }

    /**
     * Fetch and validate update information
     *
     * @return array
     *
     * @throws ValidationException
     */
    protected function fetchInfo(): array
    {
        $infoFilePath = $this->folder->getPath('/info.php');
        if (is_file($infoFilePath)) {
            $info = include $infoFilePath;

            if (isset($info['version']) && isset($info['for']) && isset($info['path']['sql']) && isset($info['path']['files']) && isset($info['path']['modules']) && isset($info['path']['themes'])) {
                return $info;
            }
            throw new ValidationException(translate('Information file ({0}) is invalid', ['info.php']));
        }
        throw new ValidationException(translate('Information file ({0}) not found', ['info.php']));
    }

    /**
     * Validate version compatibility.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    protected function validateVersion(): bool
    {
        if ($this->newVersion === $this->version) {
            throw new ValidationException(translate('The CMS is already up to date'));
        }

        foreach ($this->info['for'] as $supportedVersion) {
            if ($supportedVersion === $this->version) {
                return true;
            }
        }

        throw new ValidationException(translate('The version ({0}) of the CMS is not supported', [$this->config()->get('app')->get('version')]));
    }

    /**
     * Install extension updates
     * @return bool
     */
    public function installExtensionUpdates(): bool
    {
        if ($this->updateModules() && $this->updateThemes()) {
            return $this->folder->delete();
        }
        return false;
    }

    /**
     * Update modules.
     *
     * @return bool
     */
    protected function updateModules(): bool
    {
        foreach ($this->info['modules'] as $identifier => $packageName) {
            try {
                $packageFile = $this->folder->findFiles($this->info['path']['modules'] . '/' . $packageName)->first();
                if ($packageFile) {
                    $module = ModuleModel::findByColumn('identifier', $identifier);
                    if ($module) {
                        $module->installUpdate($packageFile);
                    } else {
                        $module = new ModuleModel();
                        $module->install($packageFile);
                    }
                    $packageFile->delete();
                }
            } catch (Throwable $ex) {
                $this->logger()->warning('Module update installation for ' . $packageName . ' failed.', [
                    'Exception message' => $ex->getMessage(),
                ]);
            }
        }

        return true;
    }

    /**
     * Update themes.
     *
     * @return bool
     */
    protected function updateThemes(): bool
    {
        foreach ($this->info['themes'] as $identifier => $packageName) {
            try {
                $packageFile = $this->folder->findFiles($this->info['path']['themes'] . '/' . $packageName)->first();
                if ($packageFile) {
                    $theme = ThemeModel::findByColumn('identifier', $identifier);
                    if ($theme) {
                        $theme->installUpdate($packageFile);
                    } else {
                        $theme = new ThemeModel();
                        $theme->install($packageFile);
                    }
                    $packageFile->delete();
                }
            } catch (Throwable $ex) {
                $this->logger()->warning('Theme update installation for ' . $packageName . ' failed.', [
                    'Exception message' => $ex->getMessage(),
                ]);
            }
        }

        return true;
    }

    /**
     * Old install method (only needed for update from 1.0.0-a1 to 1.0.0-a2).
     *
     * @return bool
     */
    public function install(): bool
    {
        return $this->installUpdate();
    }

    /**
     * Update files and database.
     *
     * @return bool
     */
    public function installUpdate(): bool
    {
        $this->validateVersion();

        $url = generate_url('backend_maintenance_index');

        $this->session()->setNewFlash('updateFolderPath', $this->folder->getPath());

        try {
            $this->updateDatabase();
            if ($this->updateFiles() && $this->updateConfig()) {
                $this->cache()->clear();
                header('Location:' . $url);
                exit;
            }
        } catch (Throwable $ex) {
            $this->logger()->error('Update installation failed.', [
                'Exception message' => $ex->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Update CMS files.
     *
     * @return bool
     */
    protected function updateFiles(): bool
    {
        $filesFolder = $this->folder->findFolders($this->info['path']['files'])->first();

        if ($filesFolder && $filesFolder->copyContent($this->config()->getPath())) {

            // Delete not needed framework folder
            return Folder::unlink($this->config()->getPath('/framework'));
        }
        return false;
    }

    /**
     * Update CMS database.
     *
     * @return bool
     */
    protected function updateDatabase(): bool
    {
        $sqlFilePath = $this->folder->getPath($this->info['path']['sql']);

        return (bool) $this
                        ->database()
                        ->executeFile($sqlFilePath);
    }

    /**
     * Update CMS config.
     *
     * @return bool
     */
    protected function updateConfig(): bool
    {
        // Backup config
        $configFilePath = $this->config()->getPath('/config.php');
        File::load($configFilePath)->rename('config-backup-' . date('d-m-Y') . '.php');

        // Get config
        $config = $this->config();

        // Update config params
        $config->get('app')->set('version', $this->newVersion);

        // Add update service
        $services = $config->get('services');
        $services[] = 'Neoflow\\CMS\\Service\\UpdateService';
        $config->set('services', $services);

        // Replace auto with true
        if ($config->get('cache')->get('type') === 'auto') {
            $config->get('cache')->set('type', true);
        }

        // Save config file
        $config->saveAsFile();

        // Set config to app
        $this->app()->set('config', $config);

        return true;
    }

}
