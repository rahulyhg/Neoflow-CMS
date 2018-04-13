<?php
namespace Neoflow\CMS\Manager;

use Neoflow\CMS\AppTrait;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;

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
     * @param string $sqlFilePath File path of sql file
     *
     * @return bool
     */
    protected function updateDatabase(string $sqlFilePath): bool
    {
        return (bool) $this
                ->database()
                ->executeFile($sqlFilePath);
    }

    /**
     * Update CMS version.
     *
     * @return bool
     */
    protected function updateVersion(): bool
    {
        $this->config()->get('app')->set('version', $this->newVersion);

        return $this->config()->saveAsFile();
    }

    /**
     * Update CMS files.
     *
     * @param string $filesDirectoryPath Directory path of new CMS files and folders
     *
     * @return bool
     */
    protected function updateFiles(string $filesDirectoryPath): bool
    {
        // Backup application config
        $applicationConfig = $this->config()->getApplicationPath('/config.php');
        File::load($applicationConfig)->rename('config-backup-' . date('d-m-Y') . '.php');

        // Copy/update files
        return (bool) Folder::load($filesDirectoryPath)->copyContent($this->config()->getPath());
    }

    /**
     * Install update.
     *
     * @return bool
     */
    public function install(): bool
    {
        $sqlFilePath = $this->folder->getPath($this->info['sql']);
        $this->updateDatabase($sqlFilePath);

        $filesDirectoryPath = $this->folder->getPath($this->info['files']);
        $this->updateFiles($filesDirectoryPath);

        $this->updateVersion();

        return true;
    }
}
