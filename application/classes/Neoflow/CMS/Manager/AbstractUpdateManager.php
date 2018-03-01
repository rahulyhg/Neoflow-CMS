<?php
namespace Neoflow\CMS\Manager;

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
     * Constructor.
     *
     * @param Folder $folder Update folder
     * @param array  $info   Info data
     */
    public function __construct(Folder $folder, array $info)
    {
        $this->folder = $folder;
        $this->info = $info;
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
     * @param string $versionNumber  Version number
     * @param string $versionRelease Version release (e.g. dev, alpha, beta, ...)
     *
     * @return bool
     */
    protected function updateVersion(string $versionNumber, string $versionRelease = ''): bool
    {
        return (bool) $this
                ->settings()
                ->setReadOnly(false)
                ->update([
                    'version_number' => $versionNumber,
                    'version_release' => $versionRelease,
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

        $this->updateVersion($this->info['version'][0], $this->info['version'][1]);

        return true;
    }
}
