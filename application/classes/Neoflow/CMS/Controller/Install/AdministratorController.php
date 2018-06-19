<?php

namespace Neoflow\CMS\Controller\Install;

use Neoflow\CMS\Controller\InstallController;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use Throwable;

class AdministratorController extends InstallController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $user = UserModel::create([]);
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $user = UserModel::create($data);
        }

        return $this->render('install/administrator/index', [
                'user' => $user,
        ]);
    }

    /**
     * Create action.
     *
     * @return Response
     */
    public function createAction(): Response
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Create user
            $user = UserModel::create([
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => 1,
                    'password' => $postData->get('password'),
                    'confirmPassword' => $postData->get('confirmPassword'),
            ]);

            // Validate and save user
            if ($user && $user->validate() && $user->save()) {
                $this->service('alert')->success(translate('Administrator successfully created'));
            }

            return $this->redirectToRoute('install_success');
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        } catch (Throwable $ex) {
            $this->service('alert')->danger([translate('Create failed'), [$ex->getMessage()]]);
        }

        return $this->redirectToRoute('install_administrator_index');
    }

    /**
     * Pre hook method.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        // Redirect to the next installer step
        if ($this->service('install')->administratorStatus()) {
            return $this->redirectToRoute('install_success');
        }

        // Redirect to previous installer step
        if (!$this->service('install')->settingStatus()) {
            return $this->redirectToRoute('install_website_index');
        }

        return parent::preHook();
    }
}
