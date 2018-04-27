<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class SettingController extends BackendController
{
    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        // Set title
        $this->view->setTitle(translate('Setting', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function indexAction(): Response
    {
        // Get settings or data if validation has failed
        $settings = SettingModel::findById(1);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $settings = new SettingModel($data);
        }

        if ($settings) {
            return $this->render('backend/setting/index', [
                    'setting' => $settings,
                    'languages' => LanguageModel::findAll(),
                    'themes' => ThemeModel::findAll(),
            ]);
        }
        throw new RuntimeException('Settings not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update settings action.
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
            // Update settings
            if ('security' === $postData->get('type')) {
                $settings = SettingModel::updateById([
                        'session_lifetime' => $postData->get('session_lifetime'),
                        'session_name' => $postData->get('session_name'),
                        'login_attempts' => $postData->get('login_attempts'),
                        ], 1);
            } elseif ('theme' === $postData->get('type')) {
                $settings = SettingModel::updateById([
                        'theme_id' => $postData->get('theme_id'),
                        'backend_theme_id' => $postData->get('backend_theme_id'),
                        'show_debugbar' => $postData->get('show_debugbar'),
                        'show_error_details' => $postData->get('show_error_details'),
                        'custom_css' => $postData->get('custom_css'),
                        'show_custom_css' => $postData->get('show_custom_css'),
                        'custom_js' => $postData->get('custom_js'),
                        'show_custom_js' => $postData->get('show_custom_js'),
                        ], 1);
            } else {
                $settings = SettingModel::updateById([
                        'website_title' => $postData->get('website_title'),
                        'website_description' => $postData->get('website_description'),
                        'website_keywords' => $postData->get('website_keywords') ? implode(',', $postData->get('website_keywords')) : '',
                        'website_author' => $postData->get('website_author'),
                        'default_language_id' => $postData->get('default_language_id'),
                        'website_emailaddress' => $postData->get('website_emailaddress'),
                        'timezone' => $postData->get('timezone'),
                        'language_ids' => $postData->get('language_ids') ?: [],
                        'allowed_file_extensions' => $postData->get('allowed_file_extensions') ? implode(',', $postData->get('allowed_file_extensions')) : '',
                        ], 1);
            }

            // Validate and save settings
            if ($settings && $settings->validate() && $settings->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Update settings failed (ID: 1)');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_setting_index');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('settings');
    }
}
