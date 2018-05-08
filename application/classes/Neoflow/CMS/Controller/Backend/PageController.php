<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\BlockModel;
use Neoflow\CMS\Model\LanguageModel;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\NavitemModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\RoleModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\View\Backend\PageView;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use function generate_url;
use function has_permission;
use function is_json;
use function translate;

class PageController extends BackendController
{
    /**
     * Constructor.
     *
     * @param PageView    $view
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(PageView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new PageView();
        }

        parent::__construct($view, $args);

        // Set title and breadcrumb
        $this->view
                ->setTitle(translate('Page', [], true))
                ->addBreadcrumb(translate('Content'));
    }

    /**
     * Index page action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Get all languages, order by default language first
        $defaultLanguageId = $this->settings()->getDefaultLanguage()->id();
        $languages = $this->settings()
                ->languages()
                ->orderByRaw('(languages.language_id = '.$defaultLanguageId.') DESC')
                ->orderByAsc('title')
                ->fetchAll();

        // Get current language
        $language_id = $this->request()->getGet('language_id');
        if (!$language_id) {
            if ($this->session()->has('language_id')) {
                $language_id = $this->session()->get('language_id');
            } else {
                $language_id = $this->settings()->getDefaultLanguage()->id();
                $this->session()->reflash();

                return $this->redirectToRoute('backend_page_index', [
                            'language_id' => $language_id,
                ]);
            }
        }
        $activeLanguage = LanguageModel::findById($language_id);
        $this->session()->set('language_id', $activeLanguage->id());

        // Get navitems
        $navitems = NavitemModel::repo()
                ->where('parent_navitem_id', 'IS', null)
                ->where('language_id', '=', $activeLanguage->id())
                ->where('navigation_id', '=', 1)
                ->orderByAsc('position')
                ->fetchAll();

        // Get blocks
        $blocks = BlockModel::repo()
                ->where('block_key', '!=', '')
                ->fetchAll();

        // Get modules
        $modules = ModuleModel::findAllByType('page');

        return $this->render('backend/page/index', [
                    'languages' => $languages,
                    'activeLanguage' => $activeLanguage,
                    'navitems' => $navitems,
                    'modules' => $modules,
                    'blocks' => $blocks,
        ]);
    }

    /**
     * Create page action.
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

            // Create page
            $page = PageModel::create([
                        'title' => $postData->get('title'),
                        'language_id' => $postData->get('language_id'),
                        'is_active' => $postData->get('is_active'),
                        'parent_navitem_id' => $postData->get('parent_navitem_id'),
                        'custom_slug' => '',
            ]);

            // Validate and save page
            if ($page && $page->validate() && $page->save() && $page->saveUrl()) {
                // Create section
                $section = SectionModel::create([
                            'page_id' => $page->id(),
                            'module_id' => $postData->get('module_id'),
                            'is_active' => true,
                            'block_id' => $postData->get('block_id'),
                ]);

                // Validate and save section
                if ($section && $section->validate() && $section->save()) {
                    $this->view->setSuccessAlert(translate('Successfully created'));

                    $module = $section->getModule();

                    return $this->redirectToRoute($module->backend_route, ['section_id' => $section->id()]);
                } else {
                    throw new RuntimeException('Creating section failed');
                }
            } else {
                throw new RuntimeException('Creating page failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_page_index');
    }

    /**
     * Edit page action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get page or data if validation has failed
        $page = PageModel::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $page = PageModel::updateById($data, $data['page_id']);
        }

        if ($page) {
            // Set title and breadcrumb
            $this->view
                    ->setTitle($page->title)
                    ->setSubtitle('ID: '.$page->id())
                    ->addBreadcrumb(translate('Page', [], true), generate_url('backend_page_index', [
                        'language_id' => $page->language_id,
            ]));

            // Set back and preview url
            $this->view
                    ->setBackRoute('backend_page_index', [
                        'language_id' => $page->language_id,
                    ])
                    ->setPreviewUrl($page->getUrl());

            // Get navitems
            $navitems = NavitemModel::repo()
                    ->where('parent_navitem_id', 'IS', null)
                    ->where('language_id', '=', $page->language_id)
                    ->where('navigation_id', '=', 1)
                    ->orderByAsc('position')
                    ->fetchAll();

            // Get navitem of page
            $pageNavitem = $page->navitems()
                    ->where('navigation_id', '=', 1)
                    ->fetch();

            // Get all roles except admin
            $roles = RoleModel::repo()->where('role_id', '!=', 1)->fetchAll();

            // Get all roles except admin
            $users = new EntityCollection();

            RoleModel::findAll()->each(function (RoleModel $role) use ($users) {
                $hasPermission = (bool) $role->permissions()->where('permission_key', '=', 'manage_pages')->count();
                if ($hasPermission) {
                    $users2 = $role->users()->fetchAll();
                    $users->merge($users2);
                }
            });

            try {
                $urlMessage = '';
                $page->validateUrl();
            } catch (ValidationException $ex) {
                $urlMessage = $ex->getMessage();
            }

            return $this->render('backend/page/edit', [
                        'page' => $page,
                        'users' => $users,
                        'pageNavitem' => $pageNavitem,
                        'navitems' => $navitems,
                        'roles' => $roles,
                        'urlMessage' => $urlMessage,
            ]);
        }

        throw new RuntimeException('Page not found (ID: '.$this->args['id'].')');
    }

    /**
     * Reorder page action.
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
                    ->getService('page')
                    ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse([
            'success' => $result,
        ]);
    }

    /**
     * Delete page action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Delete page
        $result = PageModel::deleteById($this->args['id']);
        if ($result) {
            $this->view->setSuccessAlert(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_page_index');
        }
        throw new RuntimeException('Deleting page failed (ID: '.$this->args['id'].')');
    }

    /**
     * Update page action.
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

            // Get page by id
            $page = PageModel::updateById([
                        'title' => $postData->get('title'),
                        'is_active' => $postData->get('is_active'),
                        'parent_navitem_id' => $postData->get('parent_navitem_id'),
                        'is_visible' => $postData->get('is_visible'),
                        'keywords' => $postData->get('keywords'),
                        'description' => $postData->get('description'),
                        'custom_slug' => $postData->get('custom_slug'),
                        'navigation_title' => $postData->get('navigation_title'),
                        'author_user_id' => $postData->get('author_user_id') ?: null,
                        'role_ids' => $postData->get('role_ids') ?: [],
                        'only_logged_in_users' => $postData->get('only_logged_in_users'),
                            ], $postData->get('page_id'));

            // Validate and save page
            if ($page && $page->validate() && $page->save() && $page->saveUrl()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating page failed (ID: '.$postData->get('page_id').')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_page_edit', [
                    'id' => $page->id(),
        ]);
    }

    /**
     * Toggle page activation action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function toggleActivationAction(): RedirectResponse
    {
        // Get page and toggle activation
        $page = PageModel::findById($this->args['id']);
        if ($page && $page->toggleActivation() && $page->save()) {
            if ($page->is_active) {
                $this->view->setSuccessAlert(translate('Successfully enabled'));
            } else {
                $this->view->setSuccessAlert(translate('Successfully disabled'));
            }

            return $this->redirectToRoute('backend_page_index');
        }
        throw new RuntimeException('Toggling activation for page failed (ID: '.$this->args['id'].')');
    }

    /**
     * Toggle page visibility action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function toggleVisibilityAction(): RedirectResponse
    {
        // Get page and toggle activation
        $page = PageModel::findById($this->args['id']);
        if ($page) {
            $mainNavitem = $page->getMainNavitem();
            if ($mainNavitem && $mainNavitem->toggleVisibility() && $mainNavitem->save()) {
                if ($mainNavitem->is_visible) {
                    $this->view->setSuccessAlert(translate('Successfully made visible'));
                } else {
                    $this->view->setSuccessAlert(translate('Successfully hidden'));
                }

                return $this->redirectToRoute('backend_page_index');
            }
        }
        throw new RuntimeException('Toggling visibility of navigation item for page failed');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('manage_pages');
    }
}
