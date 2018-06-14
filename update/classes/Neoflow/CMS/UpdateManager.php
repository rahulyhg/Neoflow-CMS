<?php

namespace Neoflow\CMS;

use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;
use Throwable;
use function generate_url;
use function translate;

class UpdateManager
{
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
     * Fetch and validate update information.
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
            throw new ValidationException(translate('Information file "{0}" is invalid', ['info.php']));
        }
        throw new ValidationException(translate('Information file "{0}" not found', ['info.php']));
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

        throw new ValidationException(translate('The version {0} of the CMS is not supported', [$this->config()->get('app')->get('version')]));
    }

    /**
     * Install extension updates (update step 2).
     *
     * @return bool
     */
    public function installExtensionUpdates(): bool
    {
        if ($this->updateModules() && $this->updateThemes()) {
            $this->folder->delete();

            $this->service('alert')->success(translate('CMS successfully updated'));
            $this->cache()->clear();
            header('Location:'.generate_url('backend_maintenance_index'));
            exit;
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
            $file = $this->folder->findFiles($this->info['path']['modules'].'/'.$packageName)->first();
            try {
                if ($file) {
                    $module = ModuleModel::findByColumn('identifier', $identifier);
                    if ($module) {
                        $module->installUpdatePackage($file);
                    } else {
                        ModuleModel::installPackage($file);
                    }
                }
            } catch (Throwable $ex) {
                $this->logger()->warning('Module update installation for '.$packageName.' failed.', ['Exception message' => $ex->getMessage()]);
            } finally {
                $file->delete();
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
                $file = $this->folder->findFiles($this->info['path']['themes'].'/'.$packageName)->first();
                if ($file) {
                    $theme = ThemeModel::findByColumn('identifier', $identifier);
                    if ($theme) {
                        $theme->installUpdate($file);
                    } else {
                        ThemeModel::installPackage($file);
                    }
                }
            } catch (Throwable $ex) {
                $this->logger()->warning('Theme update installation for '.$packageName.' failed.', ['Exception message' => $ex->getMessage()]);
            } finally {
                $file->delete();
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

        $this->database()->executeFile($sqlFilePath);

        return true;
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
        File::load($configFilePath)->rename('config-backup-'.date('d-m-Y_h-i-s').'.php');

        // Get config
        $config = $this->config();

        // Update config params
        $config->get('app')->set('version', $this->newVersion);

        // Register services
        $config->set('services', [
            'alert' => 'Neoflow\\CMS\\Service\\AlertService',
            'mail' => 'Neoflow\\CMS\\Service\\MailService',
            'navitem' => 'Neoflow\\CMS\\Service\\NavitemService',
            'section' => 'Neoflow\\CMS\\Service\\SectionService',
            'auth' => 'Neoflow\\CMS\\Service\\AuthService',
            'page' => 'Neoflow\\CMS\\Service\\PageService',
            'upload' => 'Neoflow\\CMS\\Service\\UploadService',
            'filesystem' => 'Neoflow\\CMS\\Service\\FilesystemService',
            'validation' => 'Neoflow\\CMS\\Service\\ValidationService',
            'install' => 'Neoflow\\CMS\\Service\\InstallService',
            'update' => 'Neoflow\\CMS\\Service\\UpdateService',
        ]);

        // Replace auto with true
        if ('auto' === $config->get('cache')->get('type')) {
            $config->get('cache')->set('type', true);
        }

        // Save config file
        $config->saveAsFile();

        // Set config to app
        $this->app()->set('config', $config);

        return true;
    }
}
