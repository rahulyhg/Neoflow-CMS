<?php

namespace Neoflow\Module\TinyMCE\Controller;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\View\Backend\SectionView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;

class ApiController extends BackendController
{
    /**
     * @var string
     */
    protected $dirPath;

    /**
     * @var string
     */
    protected $dirUrl;

    /**
     * Constructor.
     *
     * @param SectionView $view Section view
     * @param array       $args Request arguments
     *
     * @throws RuntimeException
     */
    public function __construct(SectionView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->dirPath = $this->config()->getMediaPath('/public');
        $this->dirUrl = $this->config()->getMediaUrl('/public');

        $key = $this->request()->getGet('key');
        if ($key) {
            $options = $this->session()->get($key);
            if (isset($options['uploadDirectory']['path']) && isset($options['uploadDirectory']['url'])) {
                $this->dirPath = $options['uploadDirectory']['path'];
                $this->dirUrl = $options['uploadDirectory']['url'];
            }
        }
    }

    /**
     * Upload file action.
     *
     * @return JsonResponse
     */
    public function uploadFileAction(): JsonResponse
    {
        $result = $this->service('wysiwyg')->uploadFile($this->dirPath, $this->dirUrl);

        return new JsonResponse($result);
    }

    /**
     * Get files.
     *
     * @return JsonResponse
     */
    public function filesAction(): JsonResponse
    {
        $result = $this->service('wysiwyg')->getFiles($this->dirPath, $this->dirUrl);

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
        $result = $this->service('wysiwyg')->deleteFile($this->dirPath);

        return new JsonResponse($result);
    }
}
