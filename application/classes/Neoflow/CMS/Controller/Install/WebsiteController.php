<?php

namespace Neoflow\CMS\Controller\Install;

use Neoflow\CMS\Controller\InstallController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use Throwable;

class WebsiteController extends InstallController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Get settings or data if validation has failed
        $settings = $this->settings()->setReadOnly(false);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            foreach ($data as $key => $value) {
                $settings->{$key} = $value;
            }
        }

        return $this->render('install/website/index', [
                    'setting' => $settings,
                    'languages' => LanguageModel::findAll(),
                    'activeLanguage' => $this->translator()->getActiveLanguage(),
        ]);
    }

    /**
     * Create action.
     *
     * @return Response
     */
    public function createAction(): Response
    {
        // Get post data
        $postData = $this->request()->getPostData();

        try {
            // Update settings
            $settings = SettingModel::updateById([
                        'website_title' => $postData->get('website_title'),
                        'default_language_id' => $postData->get('default_language_id'),
                        'website_emailaddress' => $postData->get('website_emailaddress'),
                        'timezone' => $postData->get('timezone'),
                        'language_ids' => $postData->get('language_ids', []),
                            ], 1);

            // Validate and save settings
            if ($settings->validate() && $settings->save()) {
                $this->view->setSuccessAlert(translate('Website successfully configured'));

                return $this->redirectToRoute('install_administrator_index');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        } catch (Throwable $ex) {
            $this->view->setDangerAlert([translate('Create failed'), [$ex->getMessage()]]);
        }

        return $this->redirectToRoute('install_website_index');
    }

    /**
     * Pre hook method.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        // Redirect to the next installer step
        if ($this->getService('install')->settingStatus()) {
            return $this->redirectToRoute('install_administrator_index');
        }

        // Redirect to the previous installer step
        if (!$this->getService('install')->databaseStatus()) {
            return $this->redirectToRoute('install_index');
        }

        return parent::preHook();
    }
}
