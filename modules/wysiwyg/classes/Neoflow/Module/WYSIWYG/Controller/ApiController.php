<?php

namespace Neoflow\Module\WYSIWYG\Controller;

use Exception;
use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Service\UploadService;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Validation\ValidationException;

class ApiController extends AbstractPageModuleController
{
    /**
     * Get upload service.
     *
     * @return UploadService
     */
    protected function getUploadService(): UploadService
    {
        return $this->service('upload');
    }

    /**
     * Upload file action.
     *
     * @return JsonResponse
     */
    public function uploadFileAction(): JsonResponse
    {
        $id = $this->args['id'];

        $uploadedItem = $this->request()->getFile('file');

        $mediaUrl = $this->config()->getMediaUrl('/modules/wysiwyg');
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');
        if ($id) {
            $mediaUrl = $this->config()->getMediaUrl('/modules/wysiwyg/'.$id);
            $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/'.$id);
        }

        if (!is_dir($mediaPath)) {
            Folder::create($mediaPath);
        }

        $result = [
            'status' => false,
            'message' => '',
            'content' => '',
        ];

        try {
            $file = $this->getUploadService()->move($uploadedItem, $mediaPath, true, $this->settings()->getAllowedFileExtensions());

            $fileUrl = normalize_url($mediaUrl.'/'.$file->getName());

            $result['status'] = true;
            $result['message'] = translate('Successfully uploaded');
            $result['content'] = $fileUrl;
            $result['file'] = [
                'name' => $file->getName(),
                'size' => $file->getSize(),
                'extension' => $file->getExtension(),
                'url' => $fileUrl,
            ];
        } catch (ValidationException $ex) {
            $result['message'] = $ex->getMessage();
        } catch (Exception $ex) {
            $result['message'] = translate('Upload file(s) failed, see error message').': '.$ex->getMessage();
        }

        return new JsonResponse($result);
    }

    /**
     * Get files.
     *
     * @return JsonResponse
     */
    public function filesAction(): JsonResponse
    {
        $id = $this->args['id'];

        $result = [];
        $mediaUrl = $this->config()->getMediaUrl('/modules/wysiwyg');
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');

        if ($id) {
            $mediaUrl = $this->config()->getMediaUrl('/modules/wysiwyg/'.$id);
            $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/'.$id);
        }

        $mediaFolder = new Folder($mediaPath);

        foreach ($mediaFolder->findFiles('*.*', GLOB_MARK | GLOB_BRACE) as $file) {
            $result[] = [
                'name' => $file->getName(),
                'url' => normalize_url($mediaUrl.'/'.$file->getName()),
                'extension' => $file->getExtension(),
                'size' => $file->getSize(),
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * Get pages.
     *
     * @return JsonResponse
     */
    public function pagesAction(): JsonResponse
    {
        $result = [];
        $navigation = NavigationModel::findByColumn('navigation_key', 'page-tree');
        $languages = $this->settings()->getLanguages();
        if ($languages->count() > 1) {
            foreach ($languages as $language) {
                $result[] = [
                    'title' => translate($language->title),
                    'value' => '#',
                    'menu' => $navigation->getNavigationTree(0, 5, false, $language->id()),
                ];
            }
        } else {
            $result = $navigation->getNavigationTree(0, 5, false, $this->translator()->getCurrentLanguage()->id());
        }

        $result = array_map('array_filter_recursive', replace_array_key(replace_array_key($result, 'value', 'url'), 'menu', 'children'));

        return new JsonResponse($result);
    }

    /**
     * Delete file action.
     *
     * @return JsonResponse
     */
    public function deleteFileAction(): JsonResponse
    {
        $id = $this->args['id'];
        $fileName = str_replace('../', '', $this->request()->getGet('name'));

        $result = [
            'status' => false,
        ];
        $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg');

        if ($id) {
            $mediaPath = $this->config()->getMediaPath('/modules/wysiwyg/'.$id);
        }

        $filePath = normalize_path($mediaPath.'/'.$fileName);
        if (is_file($filePath)) {
            $file = new File($filePath);
            if ($file->delete()) {
                $result['status'] = true;
            }
        }

        return new JsonResponse($result);
    }
}
