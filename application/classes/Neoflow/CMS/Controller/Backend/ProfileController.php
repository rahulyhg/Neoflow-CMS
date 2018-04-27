<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\UserModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class ProfileController extends BackendController
{
    /**
     * @var UserModel
     */
    protected $profileUser;

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
        $this->view->setTitle(translate('Profile', [], true));

        if ($this->getService('auth')->isAuthenticated()) {
            // Get user id of authenticated user
            $user_id = $this->getService('auth')->getUser()->id();

            // Set authenticated user as profile user
            $this->profileUser = UserModel::findById($user_id);
        }
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/profile/index', [
                'user' => $this->profileUser,
        ]);
    }

    /**
     * Update profile user action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Update user
            $user = UserModel::updateById([
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    ], $this->profileUser->id());

            // Validate and save user
            if ($user && $user->validate() && $user->save()) {
                $user->setReadOnly();
                $this->session()->set('_USER', $user);

                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating profile user failed (ID: '.$this->profileUser->id().')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_profile_index');
    }

    /**
     * Update profile user password action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updatePasswordAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Update user password
            $user = UserModel::updateByIdPassword($postData->get('newPassword'), $postData->get('confirmPassword'), $this->profileUser->id());

            // Validate and save user password
            if ($user->validateNewPassword() && $user->save()) {
                $this->view->setSuccessAlert(translate('Password successfully updated'));
            } else {
                throw new RuntimeException('Updating password of profile user failed (ID: '.$this->profileUser->id().')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('backend_profile_index');
    }

    /**
     * Delete user action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Delete user
        $result = UserModel::deleteById($this->args['id']);
        if ($result) {
            $this->view->setSuccessAlert(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_user_index');
        }
        throw new RuntimeException('Deleting user failed (ID: '.$this->args['id'].')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return $this->getService('auth')->isAuthenticated();
    }
}
