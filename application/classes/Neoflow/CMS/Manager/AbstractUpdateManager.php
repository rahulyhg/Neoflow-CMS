<?php
namespace Neoflow\CMS\Manager;

use Neoflow\CMS\AppTrait;
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
    protected $versionNumber;
    protected $versionRelease;
    protected $newVersionNumber;
    protected $newVersionRelease;

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

        $this->versionNumber = $this->settings()->version_number;
        $this->versionRelease = $this->settings()->version_release;

        $this->newVersionNumber = $this->info['version'][0];
        $this->newVersionRelease = $this->info['version'][1];
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
        return (bool) $this
                ->settings()
                ->setReadOnly(false)
                ->update([
                    'version_number' => $this->newVersionNumber,
                    'version_release' => $this->newVersionRelease,
                ])
                ->setReadOnly(true);
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
