<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\BlockModel;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\View\Backend\SectionView;
use Neoflow\Framework\HTTP\Responsing\JsonResponse;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class SectionController extends BackendController
{
    /**
     * Constructor.
     *
     * @param SectionView $view
     * @param array       $args
     */
    public function __construct(SectionView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new SectionView();
        }

        parent::__construct($view, $args);

        // Set breadcrumb
        $this->view->addBreadcrumb(translate('Content'));
    }

    /**
     * Index section action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function indexAction(): Response
    {
        // Get page by id
        $page = PageModel::findById($this->args['page_id']);
        if ($page) {
            // Set title and breadcrumb
            $this->view
                ->setTitle($page->title)
                ->setSubtitle('ID: '.$page->id())
                ->addBreadcrumb(translate('Page', [], true), generate_url('backend_page_index', ['language_id' => $page->language_id]));

            // Set back and preview url
            $this->view
                ->setBackRoute('backend_page_index', [
                    'language_id' => $page->language_id,
                ])
                ->setPreviewUrl($page->getUrl());

            // Get sections
            $sections = $page->sections()
                ->orderByAsc('position')
                ->fetchAll();

            // Get blocks
            $blocks = BlockModel::repo()
                ->where('block_key', '!=', '')
                ->fetchAll();

            // Get modules
            $modules = ModuleModel::findAllByType('page');

            return $this->render('backend/section/index', [
                'page' => $page,
                'modules' => $modules,
                'sections' => $sections,
                'blocks' => $blocks,
            ]);
        }

        throw new RuntimeException('Page of sections not found (ID: '.$this->args['id'].')');
    }

    /**
     * Reorder sections action.
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
                ->service('section')
                ->updateOrder(json_decode($json, true));
        }

        return new JsonResponse([
            'success' => $result,
        ]);
    }

    /**
     * Update section action.
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

            // Update section
            $section = SectionModel::updateById([
                'is_active' => $postData->get('is_active'),
                'block_id' => $postData->get('block_id'),
            ], $postData->get('section_id'));

            // Validate and save section
            if ($section && $section->validate() && $section->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating section failed (ID: '.$postData->get('section_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_section_edit', [
            'id' => $section->id(),
        ]);
    }

    /**
     * Create section action.
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

            // Create section
            $section = SectionModel::create([
                'page_id' => $postData->get('page_id'),
                'module_id' => $postData->get('module_id'),
                'is_active' => $postData->get('is_active'),
                'block_id' => $postData->get('block_id'),
            ]);

            // Validate and save section
            if ($section->validate() && $section->save()) {
                $this->service('alert')->success(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating section failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_section_index', [
            'page_id' => $section->page_id,
        ]);
    }

    /**
     * Delete section action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Get and delete section
        $section = SectionModel::findById($this->args['id']);
        if ($section && $section->delete()) {
            $this->service('alert')->success(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_section_index', [
                'page_id' => $section->page_id,
            ]);
        }
        throw new RuntimeException('Deleting section failed (ID: '.$this->args['id'].')');
    }

    /**
     * Toggle section activation action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function toggleActivationAction(): RedirectResponse
    {
        // Get section and toggle activation
        $section = SectionModel::findById($this->args['id']);
        if ($section && $section->toggleActivation() && $section->save()) {
            if ($section->is_active) {
                $this->service('alert')->success(translate('Successfully enabled'));
            } else {
                $this->service('alert')->success(translate('Successfully disabled'));
            }

            return $this->redirectToRoute('backend_section_index', [
                'page_id' => $section->page_id,
            ]);
        }
        throw new RuntimeException('Toggling activation for section failed (ID: '.$this->args['id'].')');
    }

    /**
     * Edit section action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get section or data if validation has failed
        $section = SectionModel::findById($this->args['id']);
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $section = SectionModel::updateById($data, $data['section_id']);
        }

        if ($section) {
            $page = $section->page()->fetch();
            $module = $section->module()->fetch();
            $block = $section->block()->fetch();

            // Set title and breadcrumb
            $this->view
                ->setTitle($module->name)
                ->setSubtitle('ID: '.$section->id().' '.translate('Block').': '.($block ? $block->title : translate('Not specified')))
                ->addBreadcrumb(translate('Page', [], true), generate_url('backend_page_index', ['language_id' => $page->language_id]))
                ->addBreadcrumb($page->title, generate_url('backend_section_index', ['page_id' => $page->id()]));

            // Set back and preview url
            $this->view
                ->setBackRoute('backend_section_index', [
                    'page_id' => $page->id(),
                ])
                ->setPreviewUrl($page->getUrl().'#section-'.$section->id());

            return $this->render('backend/section/edit', [
                'section' => $section,
                'page' => $page,
                'module' => $module,
                'blocks' => BlockModel::findAll(),
            ]);
        }

        throw new RuntimeException('Section not found (ID: '.$this->args['id'].')');
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
