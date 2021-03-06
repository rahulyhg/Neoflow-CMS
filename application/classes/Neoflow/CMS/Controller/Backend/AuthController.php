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
        if ($this->service('auth')->logout()) {
            $this->service('alert')->success(translate('Successfully logged out'));

            return $this->redirectToRoute('backend_auth_login');
        }

        $this->service('alert')->danger(translate('Logout failed'));

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

        return $this->render('backend/auth/login', ['url' => $url]);
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
            if ($this->service('auth')->login($email, $password)) {
                $this->service('alert')->success(translate('Successfully logged in'));

                if ($url) {
                    return $this->redirect($url);
                }

                return $this->redirectToRoute('backend_dashboard_index');
            }
        } catch (AuthException $ex) {
            $this->service('alert')->danger($ex->getMessage());
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
            return $this->render('backend/auth/new-password', ['user' => $user]);
        }

        $this->service('alert')->danger(translate('User for password reset not found.'));

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
            if ($this->service('auth')->updatePasswordByResetKey($newPassword, $confirmPassword, $resetKey)) {
                $this->service('alert')->success(translate('Password successfully updated'));

                return $this->redirectToRoute('backend_auth_login');
            }
        } catch (AuthException $ex) {
            $this->service('alert')->warning($ex->getMessage());
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('backend_auth_new_password', ['reset_key' => $resetKey]);
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
            if ($this->service('auth')->createResetKey($email)) {
                $this->service('alert')->success(translate('Email for password reset successfully sent.'));
            } else {
                throw new RuntimeException('Resetting password failed');
            }
        } catch (AuthException $ex) {
            $this->service('alert')->danger($ex->getMessage());
        }

        return $this->redirectToRoute('backend_auth_lost_password');
    }
}
