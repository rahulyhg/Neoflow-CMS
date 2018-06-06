<?php

namespace Neoflow\Module\Sitemap\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Sitemap\Model\SettingModel;
use Neoflow\Module\Sitemap\Model\UrlModel;
use Neoflow\Module\Sitemap\Service;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use function translate;

class BackendController extends AbstractToolModuleController
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view->setTitle('Sitemap');

        $this->service = $this->service('sitemap');
    }

    /**
     * Index action.
     *
     * @return Response
     *
     * @throws \Neoflow\Filesystem\Exception\FileException
     */
    public function indexAction(): Response
    {
        $settings = SettingModel::findById(1);

        $sitemapFile = $this->service->getFile();

        return $this->render('/sitemap/index', [
                'settings' => $settings,
                'sitemapFile' => $sitemapFile,
                'changeFrequencies' => SettingModel::$changeFrequencies,
                'sitemapLifetimes' => SettingModel::$sitemapLifetimes,
        ]);
    }

    /**
     * Update sitemap settings action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateSettingsAction(): RedirectResponse
    {
        // Get post data
        $postData = $this->request()->getPostData();

        try {
            $settings = SettingModel::updateById([
                    'default_changefreq' => $postData->get('default_changefreq'),
                    'default_priority' => $postData->get('default_priority'),
                    'sitemap_lifetime' => $postData->get('sitemap_lifetime'),
                    'automated_creation' => $postData->get('automated_creation'),
                    ], 1);

            // Validate and save settings
            if ($settings && $settings->validate() && $settings->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Update settings failed (ID: 1)');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_sitemap_backend_index');
    }

    /**
     * Create sitemap action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function createAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Update snippet
            $snippet = UrlModel::updateById([
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'placeholder' => $postData->get('placeholder'),
                    'code' => $postData->get('code'),
                    ], $postData->get('snippet_id'));

            // Validate and save snippet
            if ($snippet && $snippet->validate() && $snippet->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating snippet failed (ID: '.$postData->get('snippet_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_snippets_backend_edit', [
                'id' => $postData->get('snippet_id'),
        ]);
    }

    /**
     * Delete sitemap action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     * @throws \Neoflow\Filesystem\Exception\FileException
     */
    public function deleteSitemapAction(): RedirectResponse
    {
        try {
            $sitemapFile = $this->service->getFile();
            if ($sitemapFile) {
                if ($sitemapFile->delete()) {
                    $this->service('alert')->success(translate('Successfully deleted'));
                } else {
                    throw new RuntimeException('Deleting sitemap failed');
                }
            } else {
                throw new ValidationException(translate('Sitemap does not exist and therefore cannot be deleted'));
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning([translate('Delete failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_sitemap_backend_index');
    }

    /**
     * Recreate sitemap action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function recreateSitemapAction(): RedirectResponse
    {
        try {
            if ($this->service->generateAsFile()) {
                $this->service('alert')->success(translate('Successfully recreated'));
            } else {
                throw new RuntimeException('Recreating sitemap failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning([translate('Recreate failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_sitemap_backend_index');
    }
}
