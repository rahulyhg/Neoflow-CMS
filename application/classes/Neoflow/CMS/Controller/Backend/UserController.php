<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\CMS\Model\UserModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class UserController extends BackendController
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

        // Set title and breadcrumb
        $this->view
            ->setTitle(translate('User', [], true))
            ->addBreadcrumb(translate('Account', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/user/index', [
                'roles' => RoleModel::findAll(),
                'users' => UserModel::findAll(),
        ]);
    }

    /**
     * Create user action.
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

            // Create user
            $user = UserModel::create([
                    'email' => $postData->get('email'),
                    'firstname' => $postData->get('firstname'),
                    'lastname' => $postData->get('lastname'),
                    'role_id' => $postData->get('role_id'),
                    'password' => $postData->get('password'),
                    'confirmPassword' => $postData->get('confirmPassword'),
            ]);

            // Validate and save user
            if ($user && $user->validate() && $user->save()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Create user failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_user_index');
    }

    /**
     * Edit user action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get user or data if validation has failed
        $user = UserModel::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $user = UserModel::updateById($data, $data['user_id']);
        }

        if ($user) {
            // Set title and breadcrumb
            $this->view
                ->setTitle($user->lastname ? $user->getFullname() : $user->email)
                ->setSubtitle('ID: '.$user->id())
                ->addBreadcrumb(translate('User', [], true), generate_url('backend_user_index'));

            // Set back url
            $this->view->setBackRoute('backend_user_index');

            return $this->render('backend/user/edit', [
                    'user' => $user,
                    'roles' => RoleModel::findAll(),
            ]);
        }

        throw new RuntimeException('User not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update user action.
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
                    'role_id' => $postData->get('role_id'),
                    ], $postData->get('user_id'));

            // Validate and save user
            if ($user && $user->validate() && $user->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating user failed (ID: '.$postData->get('user_id').')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_user_edit', [
                'id' => $postData->get('user_id'),
        ]);
    }

    /**
     * Update user password action.
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
            $user = UserModel::updateByIdPassword($postData->get('newPassword'), $postData->get('confirmPassword'), $postData->get('user_id'));

            // Validate and save user password
            if ($user->validateNewPassword() && $user->save()) {
                $this->view->setSuccessAlert(translate('Password successfully updated'));
            } else {
                throw new RuntimeException('Updating password of user failed (ID: '.$postData->get('user_id').')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('backend_user_edit', [
                'id' => $postData->get('user_id'),
        ]);
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
        return has_permission('manage_users');
    }
}
