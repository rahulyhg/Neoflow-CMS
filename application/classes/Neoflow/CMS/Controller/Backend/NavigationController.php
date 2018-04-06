<?php
namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class NavigationController extends BackendController
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
            ->setTitle(translate('Navigation', [], true))
            ->addBreadcrumb(translate('Content'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $navigations = NavigationModel::repo()
            ->fetchAll();

        return $this->render('backend/navigation/index', [
                'navigations' => $navigations,
        ]);
    }

    /**
     * Create navigation action.
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

            // Create navigation
            $navigation = NavigationModel::create([
                    'title' => $postData->get('title'),
                    'navigation_key' => $postData->get('navigation_key'),
            ]);

            // Validate and save navigation
            if ($navigation && $navigation->validate() && $navigation->save()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating navigation failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_navigation_index');
    }

    /**
     * Load navigations action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function loadAction(): RedirectResponse
    {
        try {
            // Get frontend theme
            $frontendTheme = $this->settings()->getFrontendTheme();

            // Validate and save navigation
            if ($frontendTheme && $frontendTheme->loadNavigations()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating navigation failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('backend_navigation_index');
    }

    /**
     * Edit navigation action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get navigation or data if validation has failed
        $navigation = NavigationModel::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $navigation = NavigationModel::updateById($data, $data['navigation_id']);
        }

        if ($navigation && $navigation->id() !== 1) {
            // Set title and breadcrumb
            $this->view
                ->setTitle(translate($navigation->title, [], false, false, false))
                ->setSubtitle('ID: ' . $navigation->id())
                ->addBreadcrumb(translate('Navigation', [], true), generate_url('backend_navigation_index'));

            // Set back url
            $this->view->setBackRoute('backend_navigation_index');

            return $this->render('backend/navigation/edit', [
                    'navigation' => $navigation,
            ]);
        }
        throw new RuntimeException('Navigation not found or not editable (ID: ' . $this->args['id'] . ')');
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

            // Update navigation
            $navigation = NavigationModel::updateById([
                    'title' => $postData->get('title'),
                    'navigation_key' => $postData->get('navigation_key'),
                    ], $postData->get('navigation_id'));

            // Validate and save navigation
            if ($navigation && 1 !== $navigation->id() && $navigation->validate() && $navigation->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating navigation failed or navigation is not updatable (ID: ' . $postData->get('navigation_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_navigation_edit', [
                'id' => $navigation->id()
        ]);
    }

    /**
     * Delete navigation action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Get and delete navigation
        $navigation = NavigationModel::findById($this->args['id']);
        if ($navigation && $navigation->id() !== 1 && $navigation->delete()) {
            $this->view->setSuccessAlert(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_navigation_index');
        }
        throw new RuntimeException('Deleting navigation failed (ID: ' . $this->args['id'] . ')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('manage_navigations');
    }
}
