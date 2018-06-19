<?php

namespace Neoflow\Module\Search\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Search\Model\SettingModel;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use function translate;

class BackendController extends AbstractToolModuleController
{
    /**
     * @var SettingModel
     */
    protected $settings;

    /**
     * Constructor.
     *
     * @param BackendView $view Backend view
     * @param array       $args Route arguments
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->settings = SettingModel::findById(1);

        $this->view->setTitle('Search');

        if ($this->settings->is_active) {
            $this->view->setPreviewUrl($this->settings->getSearchPageUrl());
        }
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('/search/backend/index', [
                'settings' => $this->settings,
        ]);
    }

    /**
     * Update action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        // Get post data
        $postData = $this->request()->getPostData();

        try {
            $this->settings->update([
                'url_path' => $postData->get('url_path'),
                'is_active' => $postData->get('is_active'),
            ]);

            // Validate and save settings
            if ($this->settings->validate() && $this->settings->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Update settings failed (ID: 1)');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_search_backend_index');
    }
}
