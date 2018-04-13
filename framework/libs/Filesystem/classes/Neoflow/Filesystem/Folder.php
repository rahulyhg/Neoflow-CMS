<?php

namespace Neoflow\Filesystem;

use Neoflow\Filesystem\Exception\FolderException;

class Folder extends AbstractObject
{
    /**
     * Constructor.
     *
     * @param string $path Folder path
     *
     * @throws FolderException
     */
    public function __construct(string $path)
    {
        if (is_dir($path)) {
            if (is_readable($path)) {
                $this->path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
            } else {
                throw new FolderException('Cannot load folder path, because ('.$path.') is not readable', FolderException::NOT_READABLE);
            }
        } else {
            throw new FolderException('Cannot load the folder path, because ('.$path.') don\'t exist', FolderException::DONT_EXIST);
        }
    }

    /**
     * Find sub files and folders matching a pattern.
     *
     * @param string $pattern the pattern
     * @param int    $flags   Glob flags
     *
     * @return Collection
     */
    public function find(string $pattern, int $flags = 0): Collection
    {
        $files = glob(normalize_path($this->path.'/'.$pattern), $flags);
        foreach ($files as $index => $file) {
            if (is_dir($file)) {
                $files[$index] = new self($file);
            } else {
                $files[$index] = new File($file);
            }
        }

        return new Collection($files);
    }

    /**
     * Find sub files matching a pattern.
     *
     * @param string $pattern glob pattern
     * @param int    $flags   Glob flags
     *
     * @return FileCollection
     */
    public function findFiles(string $pattern, int $flags = 0): FileCollection
    {
        $files = glob(normalize_path($this->path.'/'.$pattern), $flags);
        foreach ($files as $index => $file) {
            if (is_file($file)) {
                $files[$index] = new File($file);
            } else {
                unset($files[$index]);
            }
        }

        return new FileCollection($files);
    }

    /**
     * Find sub folders matching a pattern.
     *
     * @param string $pattern glob pattern
     * @param int    $flags   Glob flags
     *
     * @return FolderCollection
     */
    public function findFolders(string $pattern, int $flags = 0): FolderCollection
    {
        $folders = glob(normalize_path($this->path.'/'.$pattern), $flags);
        foreach ($folders as $index => $folder) {
            if (is_dir($folder)) {
                $folders[$index] = new self($folder);
            } else {
                unset($folders[$index]);
            }
        }

        return new FolderCollection($folders);
    }

    /**
     * Add file to the folder.
     *
     * @param File $file
     *
     * @return self
     */
    public function addFile(File $file): self
    {
        $directory = $this->getPath();
        $file->moveToDirectory($directory);

        return $this;
    }

    /**
     * Add files to the folder.
     *
     * @param Collection $files
     *
     * @return self
     */
    public function addFileS(Collection $files): self
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Get folder path.
     *
     * @params string $additionalPath
     *
     * @return string
     */
    public function getPath(string $additionalPath = ''): string
    {
        return normalize_path($this->path.'/'.$additionalPath);
    }

    /**
     * Get subfolder.
     *
     * @param string $folderName Subfolder name
     *
     * @return self
     */
    public function getSubfolder(string $folderName): self
    {
        return new self($this->getPath($folderName));
    }

    /**
     * Get subfile.
     *
     * @param string $fileName Subfile name
     *
     * @return File
     */
    public function getSubfile(string $fileName): File
    {
        return new File($this->getPath($fileName));
    }

    /**
     * Get folder name.
     *
     * @return string
     */
    public function getName(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * Get the folder directory.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    /**
     * Delete folder.
     *
     * @param bool $recursivly Set FALSE to prevent deleting all files and subfolders recursivly
     *
     * @return bool
     */
    public function delete(bool $recursivly = true): bool
    {
        if (is_writeable($this->path)) {
            if ($recursivly) {
                $files = $this->findFiles('{,.}{,..}[!.,!..]*', GLOB_MARK | GLOB_BRACE);
                foreach ($files as $file) {
                    $file->delete();
                }
                $folders = $this->findFolders('{,.}{,..}[!.,!..]*', GLOB_MARK | GLOB_BRACE | GLOB_ONLYDIR);
                foreach ($folders as $folder) {
                    $folder->delete(true);
                }
            }

            return rmdir($this->path);
        }
        throw new FolderException('Cannot delete folder, because ('.$this->path.') is not writeable', FolderException::NOT_WRITEABLE);
    }

    /**
     * Rename folder with new folder name.
     *
     * @param string $newFolderName New folder name
     *
     * @return self
     *
     * @throws FolderException
     */
    public function rename(string $newFolderName): self
    {
        $newFolderPath = normalize_path($this->getDirectory().DIRECTORY_SEPARATOR.$newFolderName);
        if ($newFolderPath === $this->getPath() || (!is_dir($newFolderPath) && $this->move($newFolderPath))) {
            return static::load($newFolderPath);
        }
        throw new FolderException('Cannot move the folder, because the new folder path ('.$newFolderPath.') already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Move folder to directory and rename it.
     *
     * @param string $newFolderPath New folder path
     *
     * @return self
     *
     * @throws FolderException
     */
    public function move(string $newFolderPath): self
    {
        if (!is_dir($newFolderPath)) {
            if (rename($this->path, $newFolderPath)) {
                return new static($newFolderPath);
            }
            throw new FolderException('Cannot move folder to the new folder path ('.$newFolderPath.') for unknown reasons');
        }
        throw new FolderException('Cannot move the folder, because the new folder path ('.$newFolderPath.') already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Move folder to new directory.
     *
     * @param string $newDirectoryPath New directory path
     *
     * @return self
     *
     * @throws FolderException
     */
    public function moveToDirectory(string $newDirectoryPath): self
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writeable($newDirectoryPath)) {
                $newFolderPath = normalize_path($newDirectoryPath.DIRECTORY_SEPARATOR.$this->getFileName());

                return $this->move($newFolderPath);
            }
            throw new FolderException('Cannot move the folder, because the directory ('.$newDirectoryPath.') is not writeable', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot move the folder, because the directory (('.$newDirectoryPath.')) don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Copy folder to new directory and rename it.
     *
     * @param string $newFolderPath New folder path
     *
     * @return Folder
     *
     * @throws FolderException
     */
    public function copy(string $newFolderPath): self
    {
        if (!is_dir($newFolderPath)) {
            $newFolder = static::create($newFolderPath);
            if ($this->copyContent($newFolder->getPath())) {
                return $newFolder;
            }
            throw new FolderException('Cannot copy folder to the new folder path ('.$newFolderPath.') for unknown reasons');
        }
        throw new FolderException('Cannot copy the folder, because a folder with the same name and path ('.$newFolderPath.') already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Copy folder to new directory path.
     *
     * @param string $newDirectoryPath New directory path
     *
     * @return Folder
     *
     * @throws FolderException
     */
    public function copyToDirectory(string $newDirectoryPath): self
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writeable($newDirectoryPath)) {
                $newFolderPath = $newDirectoryPath.DIRECTORY_SEPARATOR.$this->getFileName();
                $this->copy($newFolderPath);
            }
            throw new FolderException('Cannot copy the folder, because the new directory path ('.$newDirectoryPath.') is not writeable', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot copy the folder, because the new directory path ('.$newDirectoryPath.') don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Copy folder content (subfiles and subfolders) to new folder path.
     *
     * @param string $newFolderPath New folder path
     *
     * @return Folder
     *
     * @throws FolderException
     */
    public function copyContent(string $newFolderPath): self
    {
        if (is_dir($newFolderPath)) {
            if (recursive_copy($this->getPath(), $newFolderPath)) {
                return static::load($newFolderPath);
            }
        }
        throw new FolderException('Cannot copy folder content, because the destination folder path ('.$newFolderPath.') don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Static method: Create new folder.
     *
     * @param string $path Folder path
     *
     * @return self
     *
     * @throws FolderException
     */
    public static function create(string $path)
    {
        $path = normalize_path($path);
        if (!is_dir($path)) {
            if (is_writeable(dirname($path))) {
                if (mkdir($path)) {
                    return new static($path);
                }
                throw new FolderException('Creating folder ('.$path.') failed');
            }
            throw new FolderException('Cannot create folder, because the directory ('.dirname($path).') is not accessible', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot create folder, because the folder path ('.$path.') already exist', FolderException::ALREADY_EXIST);
    }

    /**
     * Static method: Delete folder recursivly.
     *
     * @param string $path       Folder path
     * @param bool   $recursivly Set FALSE to prevent deleting all files and subfolders recursivly
     *
     * @return bool
     */
    public static function unlink($path, $recursivly = true)
    {
        return static::load($path)->delete($recursivly);
    }
}
