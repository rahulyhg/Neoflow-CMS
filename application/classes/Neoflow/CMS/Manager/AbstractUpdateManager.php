<?php
namespace Neoflow\CMS\Manager;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Throwable;

abstract class AbstractUpdateManager
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
     * @param array  $info   Info data
     */
    public function __construct(Folder $folder, array $info)
    {
        $this->folder = $folder;
        $this->info = $info;

        $this->version = $this->config()->get('app')->get('version');
        $this->newVersion = $this->info['version'];
    }

    /**
     * Update CMS database.
     *
     * @return bool
     */
    protected function updateDatabase(): bool
    {
        $sqlFilePath = $this->folder->getPath($this->info['sql']);

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

        // Save config file
        $config->saveAsFile();

        // Set config to app
        $this->app()->set('config', $config);

        return true;
    }

    /**
     * Update files.
     *
     * @return bool
     */
    protected function updateFiles(): bool
    {
        $filesDirectoryPath = $this->folder->getPath($this->info['files']);

        // Copy/update files
        return (bool) Folder::load($filesDirectoryPath)->copyContent($this->config()->getPath());
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
                $packageFile = $this->folder->findFiles($this->info['path']['packages'] . '/modules/' . $packageName)->first();
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
                $packageFile = $this->folder->findFiles($this->info['path']['packages'] . '/themes/' . $packageName)->first();
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
}
