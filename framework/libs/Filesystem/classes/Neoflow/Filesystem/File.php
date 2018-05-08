<?php

namespace Neoflow\Filesystem;

use Neoflow\Filesystem\Exception\FileException;
use Neoflow\Filesystem\Exception\FolderException;

class File extends AbstractObject
{
    /**
     * Constructor.
     *
     * @param string $path File path
     *
     * @throws FileException
     */
    public function __construct(string $path)
    {
        if (is_file($path)) {
            if (is_readable($path)) {
                $this->path = normalize_path($path);
            } else {
                throw new FileException('Cannot load file path, because ('.$path.') isn\'t readable', FileException::NOT_READABLE);
            }
        } else {
            throw new FileException('Cannot load the file path, because ('.$path.') don\'t exist', FileException::DONT_EXIST);
        }
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Get file content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return file_get_contents($this->path);
    }

    /**
     * Set file content.
     *
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content): self
    {
        file_put_contents($this->path, $content);

        return $this;
    }

    /**
     * Static method: Create new file.
     *
     * @param string $path      File path
     * @param string $data      File data content
     * @param bool   $overwrite Set FALSE to prevent overwriting, when the a file with the new file path already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public static function create(string $path, string $data = '', bool $overwrite = true): self
    {
        $path = normalize_path($path);

        if (is_file($path) && $overwrite) {
            static::unlink($path);
        }
        if (!is_file($path)) {
            if (is_writeable(dirname($path))) {
                if ($handle = fopen($path, 'w')) {
                    fwrite($handle, $data);
                    fclose($handle);

                    return new static($path);
                }
                throw new FileException('Creating file ('.$path.') failed');
            }
            throw new FileException('Cannot create file, because the directory ('.dirname($path).') is not accessible', FileException::NOT_WRITEABLE);
        }
        throw new FileException('Cannot create file, because the file path ('.$path.') already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Get the file name.
     *
     * @param bool $withExtension Set FALSE for file name without extension
     *
     * @return string
     */
    public function getName(bool $withExtension = true): string
    {
        if ($withExtension) {
            return pathinfo($this->path, PATHINFO_BASENAME);
        }

        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Get file modification time.
     *
     * @return int
     */
    public function getModificationTime(): int
    {
        return filemtime($this->path);
    }

    /**
     * Get the file size.
     *
     * @return int
     */
    public function getSize(): int
    {
        return filesize($this->path);
    }

    /**
     * Get formatted size as KB, MB or GB.
     *
     * @return string
     */
    public function getFormattedSize(): string
    {
        $kiloBytes = $this->getSize() / 1024;
        if ($kiloBytes < 1024) {
            return round($kiloBytes, 2).' KB';
        }
        $megaBytes = $kiloBytes / 1024;
        if ($megaBytes < 1024) {
            return round($megaBytes, 1).' MB';
        }

        return round($megaBytes / 1024, 1).' GB';
    }

    /**
     * Get mime content type.
     *
     * @return string
     */
    public function getMimeContentType(): string
    {
        return mime_content_type($this->path);
    }

    /**
     * Get the file directory.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Delete file.
     *
     * @return bool
     *
     * @throws FileException
     */
    public function delete(): bool
    {
        if (is_file($this->path)) {
            if (is_writable($this->path)) {
                return unlink($this->path);
            }
            throw new FileException('Cannot delete file, because ('.$this->path.') is not writeable', FileException::NOT_WRITEABLE);
        }

        return true;
    }

    /**
     * Move file to new file path.
     *
     * @param string $newFilePath New file path
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the new file path already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public function move(string $newFilePath, bool $overwrite = true): self
    {
        if ($overwrite || !is_file($newFilePath)) {
            if (rename($this->path, $newFilePath)) {
                return new static($newFilePath);
            }
            throw new FileException('Cannot move file to the new folder path ('.$newFilePath.') for unknown reasons');
        }
        throw new FileException('Cannot move file, because the new file path ('.$newFilePath.') already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Rename file with new file name.
     *
     * @param string $newFileName New file name
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the new file name already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public function rename(string $newFileName, bool $overwrite = true): self
    {
        $newFilePath = normalize_path($this->getDirectory().DIRECTORY_SEPARATOR.basename($newFileName));
        if ($newFilePath === $this->getPath() || (($overwrite || !is_file($newFilePath)) && $this->move($newFilePath))) {
            return static::load($newFilePath);
        }
        throw new FileException('Cannot rename the file, because the new file name ('.$newFileName.') already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Move file to new directory path.
     *
     * @param string $newDirectoryPath New directory path
     * @param bool   $overwrite        Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public function moveToDirectory(string $newDirectoryPath, bool $overwrite = true): self
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writable($newDirectoryPath)) {
                $newFilePath = normalize_path($newDirectoryPath.DIRECTORY_SEPARATOR.$this->getName());
                if ($overwrite || !is_file($newFilePath)) {
                    if ((is_file($newFilePath) && is_writeable($newFilePath)) || !is_file($newFilePath)) {
                        return $this->move($newFilePath, $overwrite);
                    }
                    throw new FileException('Cannot move the file, because the existing file ('.$newFilePath.') is not writable and cannot be overwritten', FileException::NOT_WRITEABLE);
                }
                throw new FileException('Cannot move the file, because a file with the same name ('.$newFilePath.') already exist', FileException::ALREADY_EXIST);
            }
            throw new FolderException('Cannot move the file, because the new directory path ('.$newDirectoryPath.') is not writeable', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot move the file, because the new directory path ('.$newDirectoryPath.') don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Copy file to new file path.
     *
     * @param string $newFilePath New file path
     * @param bool   $overwrite   Set FALSE to prevent overwriting, when the a file with the new file path already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public function copy(string $newFilePath, bool $overwrite = true): self
    {
        if ($overwrite || !is_file($newFilePath)) {
            if (copy($this->path, $newFilePath)) {
                return new static($newFilePath);
            }
            throw new FileException('Cannot copy file to the new file path ('.$newFilePath.') for unknown reasons');
        }
        throw new FileException('Cannot copy file, because the new file path ('.$newFilePath.') already exist', FileException::ALREADY_EXIST);
    }

    /**
     * Copy file to new directory path.
     *
     * @param string $newDirectoryPath New directory path
     * @param bool   $overwrite        Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     *
     * @return self
     *
     * @throws FileException
     */
    public function copyToDirectory(string $newDirectoryPath, bool $overwrite = true): self
    {
        if (is_dir($newDirectoryPath)) {
            if (is_writable($newDirectoryPath)) {
                $newFilePath = normalize_path($newDirectoryPath.DIRECTORY_SEPARATOR.$this->getName());
                if ($overwrite || !is_file($newFilePath)) {
                    if ((is_file($newFilePath) && is_writeable($newFilePath)) || !is_file($newFilePath)) {
                        return $this->copy($newFilePath, $overwrite);
                    }
                    throw new FileException('Cannot copy the file, because the existing file ('.$newFilePath.') is not writable and cannot be overwritten', FileException::NOT_WRITEABLE);
                }
                throw new FileException('Cannot copy the file, because a file with the same name ('.$newFilePath.') already exist', FileException::ALREADY_EXIST);
            }
            throw new FolderException('Cannot copy the file, because the new directory path ('.$newDirectoryPath.') is not writeable', FolderException::NOT_WRITEABLE);
        }
        throw new FolderException('Cannot copy the file, because the new directory path ('.$newDirectoryPath.') don\'t exist', FolderException::DONT_EXIST);
    }

    /**
     * Static method: Delete file.
     *
     * @param string $path File path
     *
     * @return bool
     */
    public static function unlink($path): bool
    {
        if (is_file($path)) {
            return static::load($path)->delete();
        }

        return true;
    }
}
