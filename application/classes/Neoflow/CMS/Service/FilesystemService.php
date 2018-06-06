<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
use Neoflow\Filesystem\Exception\FileException;
use Neoflow\Filesystem\Exception\FolderException;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Validation\ValidationException;

class FilesystemService extends AbstractService
{
    /**
     * Create new folder in directory.
     *
     * @param string $name          New folder path
     * @param string $directoryPath Target directory path
     *
     * @return Folder
     *
     * @throws ValidationException
     * @throws FolderException
     */
    public function createNewFolder(string $name, string $directoryPath): Folder
    {
        $name = sanitize_file_name($name, false);

        try {
            if ('' === $name) {
                throw new ValidationException(translate('Folder name must be at least one character or longer'));
            }

            return Folder::create($directoryPath.DIRECTORY_SEPARATOR.$name);
        } catch (FolderException $ex) {
            if (FolderException::ALREADY_EXIST === $ex->getCode()) {
                throw new ValidationException(translate('A folder with the name "{0}" already exists', [$name]));
            }
            throw $ex;
        }
    }

    /**
     * Move file to directory.
     *
     * @param File   $file          Movable file
     * @param string $directoryPath New directory path
     * @param bool   $overwrite     Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     *
     * @return File
     *
     * @throws ValidationException
     * @throws FileException
     */
    public function moveFileToDirectory(File $file, string $directoryPath, bool $overwrite = true): File
    {
        try {
            return $file->moveToDirectory($directoryPath, $overwrite);
        } catch (FileException $ex) {
            if (FileException::ALREADY_EXIST === $ex->getCode()) {
                throw new ValidationException(translate('A file with the name "{0}" already exists', [$file->getName()]));
            }
            throw $ex;
        }
    }

    /**
     * Copy file to directory.
     *
     * @param File   $file          Copiable file
     * @param string $directoryPath New directory path
     * @param bool   $overwrite     Set FALSE to prevent overwriting, when a file with same name in the new directory path already exist
     *
     * @return File
     *
     * @throws ValidationException
     * @throws FileException
     */
    public function copyFileToDirectory(File $file, string $directoryPath, bool $overwrite = true): File
    {
        try {
            return $file->copyToDirectory($directoryPath, $overwrite);
        } catch (FileException $ex) {
            if (FileException::ALREADY_EXIST === $ex->getCode()) {
                throw new ValidationException(translate('A file with the name "{0}" already exists', [$file->getName()]));
            }
            throw $ex;
        }
    }

    /**
     * Rename file.
     *
     * @param File   $file      File to rename
     * @param string $name      New file name
     * @param bool   $overwrite Set FALSE to prevent overwriting, when a file with same name already exist
     *
     * @return File
     *
     * @throws ValidationException
     * @throws FileException
     */
    public function renameFile(File $file, string $name, bool $overwrite = true): File
    {
        $name = sanitize_file_name($name, false);

        try {
            return $file->rename($name, $overwrite);
        } catch (FileException $ex) {
            if (FileException::ALREADY_EXIST === $ex->getCode()) {
                throw new ValidationException(translate('A file with the name "{0}" already exists', [$name]));
            }
            throw $ex;
        }
    }

    /**
     * Rename folder.
     *
     * @param Folder $folder Folder to rename
     * @param string $name   New folder name
     *
     * @return Folder
     *
     * @throws ValidationException
     * @throws FolderException
     */
    public function renameFolder(Folder $folder, string $name): Folder
    {
        $name = sanitize_file_name($name, false);

        try {
            return $folder->rename($name);
        } catch (FolderException $ex) {
            if (FolderException::ALREADY_EXIST === $ex->getCode()) {
                throw new ValidationException(translate('A folder with the name "{0}" already exists', [$name]));
            }
            throw $ex;
        }
    }
}
