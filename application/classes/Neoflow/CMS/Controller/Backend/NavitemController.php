<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\NavigationModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\View\Backend\NavitemView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class NavitemController extends BackendController
{
    /**
     * Constructor.
     *
     * @paran NavitemView $view
     *
     * @param array $args
     */
    public function __construct(NavitemView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new NavitemView();
        }

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
     *
     * @throws RuntimeException
     */
    public function indexAction(): Response
    {
        // Get navigation by id
        $navigation = NavigationModel::findById($this->args['id']);
        if ($navigation) {
            // Get languages
            $languages = $this->settings()->getLanguages();

            // Get id of current language
            $language_id = $this->request()->getGet('language_id');
            if (!$language_id) {
                if ($this->session()->has('language_id')) {
                    $language_id = $this->session()->get('language_id');
                } else {
                    $language_id = $this->settings()->getDefaultLanguage()->id();
                    $this->session()->reflash();

                    return $this->redirectToRoute('backend_navitem_index', [
                            'id' => $navigation->id(),
                            'language_id' => $language_id,
                    ]);
                }
            }
            $this->session()->set('language_id', $language_id);

            // Get navigation items for selectable pages
            $pageNavitems = NavitemModel::repo()
                ->where('navigation_id', '=', 1)
                ->where('language_id', '=', $language_id)
                ->where('parent_navitem_id', 'IS', null)
                ->orderByAsc('position')
                ->fetchAll();

            // Get language of navigation
            $navigationLanguage = LanguageModel::findById($language_id);

            // Get navigation items of navigation and current language
            $navitems = $navigation->navitems()
                ->where('parent_navitem_id', 'IS', null)
                ->where('language_id', '=', $language_id)
                ->where('parent_navitem_id', 'IS', null)
                ->orderByAsc('position')
                ->fetchAll();

            // Set title and breadcrumb
            $this->view
                ->setTitle(translate($navigation->title, [], false, false, false))
                ->setSubtitle('ID: '.$navigation->id())
                ->addBreadcrumb(translate('Navigation', [], true), generate_url('backend_navigation_index'));

            // Set back url
            $this->view->setBackRoute('backend_navigation_index');

            return $this->render('backend/navitem/index', [
                    'navigation' => $navigation,
                    'navitems' => $navitems,
                    'pageNavitems' => $pageNavitems,
                    'languages' => $languages,
                    'navigationLanguage' => $navigationLanguage,
            ]);
        }
        throw new RuntimeException('Navigation item not found or navigation is not editable (ID: '.$this->args['id'].')');
    }

    /**
     * Create navigation item action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function createAction(): RedirectResponse
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Create navigation item
            $navitem = NavitemModel::create([
                    'title' => $postData->get('title'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ?: null,
                    'navigation_id' => $postData->get('navigation_id'),
                    'language_id' => $postData->get('language_id'),
                    'page_id' => $postData->get('page_id'),
                    'is_active' => $postData->get('is_active'),
            ]);

            // Validate and save navigation item
            if ($navitem && 1 != $navitem->navigation_id && $navitem->validate() && $navitem->save()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating navigation item failed or navigation is not editable');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_navitem_index', [
                'id' => $navitem->navigation_id,
        ]);
    }

    /**
     * Delete navigation item action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Prevent access for page permissions
        if (has_permission('manage_pages') && !has_permission('manage_navigations')) {
            return $this->unauthorizedAction();
        }

        // Get and delete navigation item
        $navitem = NavitemModel::findById($this->args['id']);
        if ($navitem && 1 != $navitem->navigation_id && $navitem->delete()) {
            $this->view->setSuccessAlert(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_navitem_index', [
                    'id' => $navitem->navigation_id,
            ]);
        }
        throw new RuntimeException('Deleting navigation item failed or not deletable (ID: '.$this->args['id'].')');
    }

    /**
     * Edit item action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get navigation item or data if validation has failed
        $navitem = NavitemModel::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $navitemData = $this->getService('validation')->getData();
            $navitem = new NavitemModel($navitemData);
        }

        if ($navitem) {
            // Get navigation
            $navigation = $navitem->navigation()->fetch();

            if ($navigation) {
                // Set title and breadcrumb
                $this->view
                    ->setTitle($navitem->title)
                    ->setSubtitle('ID: '.$navitem->id().' '.translate('Page').': '.$navitem->page()->fetch()->title)
                    ->addBreadcrumb(translate('Navigation', [], true), generate_url('backend_navigation_index'))
                    ->addBreadcrumb($navigation->title, generate_url('backend_navitem_index', ['id' => $navigation->id()]));

                // Set back url
                $this->view->setBackRoute('backend_navitem_index', [
                    'id' => $navitem->navigation_id,
                ]);

                // Get navigation items for selectable pages
                $pageNavitems = NavitemModel::repo()
                    ->where('navigation_id', '=', 1)
                    ->where('language_id', '=', $navitem->language_id)
                    ->where('parent_navitem_id', 'IS', null)
                    ->orderByAsc('position')
                    ->fetchAll();

                // Get navigation items of navigation and current language
                $navitems = $navigation->navitems()
                    ->where('parent_navitem_id', 'IS', null)
                    ->where('language_id', '=', $navitem->language_id)
                    ->where('parent_navitem_id', 'IS', null)
                    ->orderByAsc('position')
                    ->fetchAll();

                return $this->render('backend/navitem/edit', [
                        'navitem' => $navitem,
                        'navigation' => $navigation,
                        'pageNavitems' => $pageNavitems,
                        'navitems' => $navitems,
                ]);
            }
            throw new RuntimeException('Navigation item not found (ID: '.$this->args['id'].')');
        }
        throw new RuntimeException('Navigation item not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update navigation item action.
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

            // Update navitem
            $navitem = NavitemModel::updateById([
                    'title' => $postData->get('title'),
                    'is_active' => $postData->get('is_active'),
                    'parent_navitem_id' => $postData->get('parent_navitem_id') ?: null,
                    ], $postData->get('navitem_id'));

            if (1 != $navitem->navigation_id) {
                $navitem->page_id = $postData->get('page_id');
            }

            // Validate and save navigation item
            if ($navitem && $navitem->validate() && $navitem->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating navigation item failed (ID: '.$postData->get('navitem_id').')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_navitem_edit', [
                'id' => $navitem->id(),
        ]);
    }

    /**
     * Toggle navigation item activation action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function toggleActivationAction(): RedirectResponse
    {
        // Get navigation item and toggle activation
        $navitem = NavitemModel::findById($this->args['id']);
        if ($navitem && $navitem->toggleActivation() && $navitem->save()) {
            if ($navitem->is_active) {
                $this->view->setSuccessAlert(translate('Successfully enabled'));
            } else {
                $this->view->setSuccessAlert(translate('Successfully disabled'));
            }

            return $this->redirectToRoute('backend_navitem_index', [
                    'id' => $navitem->navigation_id,
            ]);
        }
        throw new RuntimeException('Toggling activation for navigation item failed (ID: '.$this->args['id'].')');
    }

    /**
     * Reorder navigation items action.
     *
     * @return JsonResponse
     */
    public function reorderAction(): JsonResponse
    {
        // Get json request
        $json = file_get_contents('php://input');

        // Reorder and update navigation item
        $result = false;
        if (is_json($json)) {
            $result = $this
                ->getService('navitem')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse([
            'success' => $result,
        ]);
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('manage_navigations') || has_permission('manage_pages');
    }
}
