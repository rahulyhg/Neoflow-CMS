<?php
namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\PermissionModel;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class RoleController extends BackendController
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
            ->setTitle(translate('Role', [], true))
            ->addBreadcrumb(translate('Account', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Get roles
        $roles = RoleModel::repo()->fetchAll();

        return $this->render('backend/role/index', [
                'permissions' => PermissionModel::findAll(),
                'roles' => $roles,
        ]);
    }

    /**
     * Create role action.
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

            // Create role
            $role = RoleModel::create([
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids') ?: [],
            ]);

            // Validate and save role
            if ($role && $role->validate() && $role->save()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating role failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_role_index');
    }

    /**
     * Edit role action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get user or data if validation has failed
        $role = RoleModel::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $role = new RoleModel($data);
        }

        if ($role && $role->id() !== 1) {
            // Set title and breadcrumb
            $this->view
                ->setTitle($role->title)
                ->setSubtitle('ID: ' . $role->id())
                ->addBreadcrumb(translate('Role', [], true), generate_url('backend_role_index'));

            // Set back url
            $this->view->setBackRoute('backend_role_index');

            return $this->render('backend/role/edit', [
                    'role' => $role,
                    'permissions' => PermissionModel::findAll(),
            ]);
        }
        throw new RuntimeException('Role not found or not editable (ID: ' . $this->args['id'] . ')');
    }

    /**
     * Update role action.
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

            // Update role
            $role = RoleModel::updateById([
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'permission_ids' => $postData->get('permission_ids') ?: [],
                    ], $postData->get('role_id'));

            // Validate and save role
            if ($role && $role->id() !== 1 && $role->validate() && $role->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating role failed (ID: ' . $postData->get('page_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_role_edit', [
                'id' => $postData->get('role_id')
        ]);
    }

    /**
     * Delete role action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        try {
            // Get and delete role
            $role = RoleModel::findById($this->args['id']);
            if ($role && $role->id() !== 1 && $role->delete()) {
                $this->view->setSuccessAlert(translate('Successfully deleted'));
            } else {
                throw new RuntimeException('Deleting role failed (ID: ' . $this->args['id'] . ')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('backend_role_index');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('manage_roles');
    }
}
