<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Exception\AuthException;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class AuthController extends BackendController
{
    /**
     * Logout action.
     *
     * @return RedirectResponse
     */
    public function logoutAction(): RedirectResponse
    {
        if ($this->getService('auth')->logout()) {
            $this->view->setSuccessAlert(translate('Successfully logged out'));

            return $this->redirectToRoute('backend_auth_login');
        }

        $this->view->setDangerAlert(translate('Logout failed'));

        return $this->redirectToRoute('backend_dashboard_index');
    }

    /**
     * Login action.
     *
     * @return Response
     */
    public function loginAction(): Response
    {
        $url = $this->request()->getGet('url');

        $this->view->setTitle(translate('Login'));

        return $this->render('backend/auth/login', [
                'url' => $url,
        ]);
    }

    /**
     * Authentication and authorization action.
     *
     * @return RedirectResponse
     */
    public function authenticateAction(): Response
    {
        // Get post data
        $email = $this->request()->getPost('email');
        $password = $this->request()->getPost('password');
        $url = $this->request()->getPost('url');

        // Authenticate and authorize user
        try {
            if ($this->getService('auth')->login($email, $password)) {
                $this->view->setSuccessAlert(translate('Successfully logged in'));

                if ($url) {
                    return $this->redirect($url);
                }

                return $this->redirectToRoute('backend_dashboard_index');
            }
        } catch (AuthException $ex) {
            $this->view->setDangerAlert($ex->getMessage());
        }

        return $this->redirectToRoute('backend_auth_login');
    }

    /**
     * Lost password Action.
     *
     * @return Response
     */
    public function lostPasswordAction(): Response
    {
        $this->view->setTitle(translate('Reset password'));

        return $this->render('backend/auth/lost-password');
    }

    /**
     * New password Action.
     *
     * @return Response
     */
    public function newPasswordAction(): Response
    {
        $this->view->setTitle(translate('Create new password'));

        $user = UserModel::findByColumn('reset_key', $this->args['reset_key']);

        if ($user) {
            $this->view->setInfoAlert(translate('Please enter the new password for your user account, registered under the email address {0}.', [$user->email]));

            return $this->render('backend/auth/new-password', ['user' => $user]);
        }

        $this->view->setDangerAlert(translate('User not found'));

        return $this->redirectToRoute('backend_auth_login');
    }

    /**
     * Update password action.
     *
     * @return RedirectResponse
     */
    public function updatePasswordAction(): RedirectResponse
    {
        // Get post data
        $resetKey = $this->request()->getPost('reset_key');
        $newPassword = $this->request()->getPost('new_password');
        $confirmPassword = $this->request()->getPost('confirm_password');

        // Authenticate and authorize user
        try {
            if ($this->getService('auth')->updatePassword($newPassword, $confirmPassword, $resetKey)) {
                $this->view->setSuccessAlert(translate('Password successfully updated'));

                return $this->redirectToRoute('backend_auth_login');
            }
        } catch (AuthException $ex) {
            $this->view->setWarningAlert($ex->getMessage());
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('backend_auth_new_password', [
                'reset_key' => $resetKey,
        ]);
    }

    /**
     * Reset password action.
     *
     * @return RedirectResponse
     */
    public function resetPasswordAction(): RedirectResponse
    {
        $email = $this->request()->getPost('email');

        try {
            if ($this->getService('auth')->createResetKey($email)) {
                $this->view->setSuccessAlert(translate('Email successfully sent'));
            } else {
                throw new RuntimeException('Resetting password failed');
            }
        } catch (AuthException $ex) {
            $this->view->setDangerAlert($ex->getMessage());
        }

        return $this->redirectToRoute('backend_auth_lost_password');
    }
}
