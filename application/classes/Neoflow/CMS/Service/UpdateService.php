<?php

namespace Neoflow\CMS\Service;

use Neoflow\CMS\Core\AbstractService;
    use Neoflow\CMS\UpdateManager;
    use Neoflow\Filesystem\File;
    use Neoflow\Filesystem\Folder;
    use Neoflow\Validation\ValidationException;
    use RuntimeException;
    use ZipArchive;
    use function translate;

    class UpdateService extends AbstractService
    {
        /**
         * Unpack update package.
         *
         * @param File $file Update package file (Zip archive)
         *
         * @return Folder
         *
         * @throws ValidationException
         */
        protected function unpack(File $file): Folder
        {
            // Create temporary update folder
            $folderPath = $this->config()->getPath('/update_'.uniqid());
            $folder = Folder::create($folderPath);

            // Extract package
            $zipFile = new ZipArchive();
            if (true === $zipFile->open($file->getPath())) {
                $zipFile->extractTo($folderPath);
                $zipFile->close();

                // Delete update package
                $file->delete();

                return $folder;
            }
            throw new ValidationException(translate('Zip archive "{0}" is invalid', [$file->getName()]));
        }

        /**
         * Install CMS update.
         *
         * @param File $file Update package file (Zip archive)
         *
         * @return bool
         */
        public function installUpdate(File $file): bool
        {
            // Unpack update package and create folder
            $folder = $this->unpack($file);

            // Install CMS update
            return $this->manager($folder)->installUpdate();
        }

        /**
         * Install modules and themes updates.
         *
         * @param string $folderPath Update folder path
         *
         * @return bool
         */
        public function installExtensionUpdates($folderPath): bool
        {
            // Create update folder
            $folder = Folder::load($folderPath);

            // Install extension updates
            return $this->manager($folder)->installExtensionUpdates();
        }

        /**
         * Create update manager.
         *
         * @param Folder $folder Update folder
         *
         * @return UpdateManager
         *
         * @throws RuntimeException
         */
        protected function manager(Folder $folder): UpdateManager
        {
            // Add class directory to loader
            $classPath = $folder->getPath('/classes');
            if (is_dir($classPath)) {
                $this->app()->get('loader')->addClassDirectory($classPath);
            }

            if (class_exists('Neoflow\\CMS\\UpdateManager')) {
                return new UpdateManager($folder);
            }

            throw new RuntimeException('Update manager not found');
        }
    }
