<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\View\Backend\MediaView;
use Neoflow\CMS\View\BackendView;
use Neoflow\Filesystem\Exception\FilesystemException;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\HTTP\Responsing\DownloadResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;

class MediaController extends BackendController
{
    /**
     * @var string
     */
    protected $relativeFolderPath;
    protected $relativeFilePath;

    /**
     * @var Folder
     */
    protected $currentFolder;

    /**
     * @var File
     */
    protected $currentFile;

    /**
     * @var bool
     */
    protected $plain = false;

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new MediaView();
        }

        parent::__construct($view, $args);

        // Set base path
        $basePath = $this->config()->getMediaPath('/public');

        // Get request data
        $postData = $this->request()->getPostData();
        $getData = $this->request()->getGetData();

        // Get current directory
        $relativeFolderPath = '';
        $relativeFilePath = '';
        if ($getData->exists('dir')) {
            $relativeFolderPath = $getData->get('dir');
        } elseif ($getData->exists('file')) {
            $relativeFolderPath = dirname($getData->get('file'));
            $relativeFilePath = $getData->get('file');
        } elseif ($postData->exists('dir')) {
            $relativeFolderPath = $postData->get('dir');
        } elseif ($postData->exists('file')) {
            $relativeFolderPath = dirname($postData->get('file'));
            $relativeFilePath = $postData->get('file');
        }

        if ('' !== $relativeFilePath) {
            $filePath = normalize_path($basePath.DIRECTORY_SEPARATOR.$relativeFilePath);
            if (is_valid_path($filePath, $basePath) && is_file($filePath)) {
                $this->relativeFilePath = normalize_path($relativeFilePath, true);
                $this->currentFile = new File($filePath);
                $relativeFolderPath = dirname($relativeFilePath);
            } else {
                $this->service('alert')->danger([translate('File not found'), [normalize_url($relativeFilePath)]]);

                return $this->redirectToRoute('backend_media_index');
            }
        }

        $directoryPath = normalize_path($basePath.DIRECTORY_SEPARATOR.$relativeFolderPath);
        if ('' === $relativeFolderPath || (is_valid_path($directoryPath, $basePath) && is_dir($directoryPath))) {
            $this->relativeFolderPath = normalize_path($relativeFolderPath, true);
            $this->currentFolder = new Folder($directoryPath);
        } else {
            $this->service('alert')->danger([translate('Directory not found'), [normalize_url($relativeFolderPath)]]);

            return $this->redirectToRoute('backend_media_index');
        }

        // Set title and breadcrumb
        if ($this->relativeFolderPath) {
            $this->view->setBackUrl(generate_url('backend_media_index'));
            $this->view->addBreadcrumb(translate('Media', [], true), generate_url('backend_media_index'));

            $directories = [];
            $folders = explode(DIRECTORY_SEPARATOR, normalize_path($this->relativeFolderPath));
            $numberOfFolders = count($folders);
            foreach ($folders as $index => $folder) {
                if ($folder) {
                    $directories[$folder] = end($directories).DIRECTORY_SEPARATOR.$folder;
                    if ($this->currentFile || $numberOfFolders > ++$index) {
                        $this->view->addBreadcrumb($folder, generate_url('backend_media_index', ['dir' => end($directories)]));
                        $this->view->setBackUrl(generate_url('backend_media_index', ['dir' => end($directories)]));
                    }
                }
            }
        }

        $this->view->setTitle(translate('Media', [], true));
        if ($this->currentFile) {
            $this->view->setTitle($this->currentFile->getName());
        } elseif ($this->currentFolder && $this->relativeFolderPath) {
            $this->view->setTitle($this->currentFolder->getName());
        }

        $this->relativeFolderPath = ltrim($this->relativeFolderPath, '\\');
        $this->relativeFolderPath = ltrim($this->relativeFolderPath, '/');

        if ($this->request()->getGet('plain')) {
            $this->plain = true;
        }
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $files = $this->currentFolder->findFiles('[!.,!..]*', GLOB_MARK | GLOB_BRACE)->sortByName('ASC');
        $folders = $this->currentFolder->findFolders('[!.,!..]*', GLOB_MARK | GLOB_BRACE)->sortByName('ASC');

        return $this->render('backend/media/index', [
            'files' => $files,
            'folders' => $folders,
            'currentFolder' => $this->currentFolder,
            'relativeFolderPath' => $this->relativeFolderPath,
            'path' => str_replace($this->config()->getPath(), DIRECTORY_SEPARATOR, $this->currentFolder->getPath()),
        ]);
    }

    /**
     * Download action.
     *
     * @return Response
     */
    public function downloadAction(): Response
    {
        $response = new DownloadResponse();

        return $response->setFile($this->currentFile);
    }

    /**
     * Delete file action.
     *
     * @return Response
     */
    public function deleteFileAction(): Response
    {
        try {
            $this->currentFile->delete();
            $this->service('alert')->success(translate('Successfully deleted'));
        } catch (FilesystemException $ex) {
            $this->service('alert')->danger([
                translate('

        Delete file failed, see error message'),
                [$ex->getMessage()],
            ]);
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $this->relativeFolderPath]);
    }

    /**
     * Delete folder action.
     *
     * @return Response
     */
    public function deleteFolderAction(): Response
    {
        try {
            $this->currentFolder->delete(true);
            $this->service('alert')->success(translate('Successfully deleted'));
        } catch (FilesystemException $ex) {
            $this->service('alert')->danger([
                translate('Delete folder failed, see error message'),
                [$ex->getMessage()],
            ]);
        }
        $newRelativeFolderPath = normalize_path(dirname($this->relativeFolderPath), true);
        if ('.' === $newRelativeFolderPath) {
            $newRelativeFolderPath = '';
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $newRelativeFolderPath]);
    }

    /**
     * Rename folder action.
     *
     * @return Response
     */
    public function renameFolderAction(): Response
    {
        // Set back url
        $this->view->setBackUrl(generate_url('backend_media_index', ['dir' => $this->relativeFolderPath]));

        return $this->render('backend/media/rename/folder', [
            'currentFolder' => $this->currentFolder,
            'relativeFolderPath' => $this->relativeFolderPath,
        ]);
    }

    /**
     * Rename file action.
     *
     * @return Response
     */
    public function renameFileAction(): Response
    {
        // Set back url
        $this->view->setBackUrl(generate_url('backend_media_index', ['dir' => $this->relativeFolderPath]));

        return $this->render('backend/media/rename/file', [
            'currentFile' => $this->currentFile,
            'relativeFilePath' => $this->relativeFilePath,
        ]);
    }

    /**
     * Update file action.
     *
     * @return Response
     */
    public function updateFileAction(): Response
    {
        // Get file name and extension
        $name = $this->request()->getPost('name');
        $extension = $this->request()->getPost('extension');

        // Add file extension
        if ($extension) {
            $name .= '.'.$extension;
        }

        try {
            $this->service('filesystem')->renameFile($this->currentFile, $name, false);
            $this->service('alert')->success(translate('Successfully renamed'));
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Rename failed'), $ex->getErrors()]);
        } catch (FilesystemException $ex) {
            $this->service('alert')->danger([translate('Rename file failed, see error message'), [$ex->getMessage()]]);
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $this->relativeFolderPath]);
    }

    /**
     * Update folder action.
     *
     * @return Response
     */
    public function updateFolderAction(): Response
    {
        // Get file name
        $name = $this->request()->getPost('name');

        try {
            $this->service('filesystem')->renameFolder($this->currentFolder, $name, false);
            $this->service('alert')->success(translate('Successfully renamed'));
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Rename failed'), $ex->getErrors()]);
        } catch (FilesystemException $ex) {
            $this->service('alert')->danger([
                translate('Rename folder failed, see error message'),
                [$ex->getMessage()],
            ]);
        }

        $newRelativeFolderPath = normalize_path(dirname($this->relativeFolderPath), true);
        if ('.' === $newRelativeFolderPath) {
            $newRelativeFolderPath = '';
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $newRelativeFolderPath]);
    }

    /**
     * Upload action.
     *
     * @return Response
     */
    public function uploadAction(): Response
    {
        $directoryPath = $this->currentFolder->getPath();

        $uploadedItems = $this->request()->getFile('files');
        $overwrite = $this->request()->getPost('overwrite');

        try {
            $result = $this->service('upload')->moveMultiple($uploadedItems, $directoryPath, $overwrite, $this->settings()->getAllowedFileExtensions());
            if (count($result['success'])) {
                $this->service('alert')->success([translate('Successfully uploaded'), array_keys($result['success'])]);
            }
            if (count($result['error'])) {
                $this->service('alert')->danger([translate('Upload failed'), $result['error']]);
            }
        } catch (Exception $ex) {
            $this->service('alert')->danger([
                translate('Upload file(s) failed, see error message'),
                [$ex->getMessage()],
            ]);
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $this->relativeFolderPath]);
    }

    /**
     * Create folder action.
     *
     * @return Response
     */
    public function createFolderAction(): Response
    {
        // Get folder name
        $name = $this->request()->getPost('name');

        try {
            $this->service('filesystem')->createNewFolder($name, $this->currentFolder->getPath());
            $this->service('alert')->success(translate('Successfully created'));
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        } catch (FilesystemException $ex) {
            $this->service('alert')->danger([
                translate('Create folder failed, see error message'),
                [$ex->getMessage()],
            ]);
        }

        return $this->redirectToRoute('backend_media_index', ['dir' => $this->relativeFolderPath]);
    }
}
