<?php
namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\View\Backend\SectionView;
use Neoflow\Framework\HTTP\Responsing\Response;
use RuntimeException;

abstract class AbstractPageModuleController extends SectionController
{

    /**
     * Constructor.
     *
     * @param SectionView $view
     * @param array       $args
     *
     * @throws RuntimeException
     */
    public function __construct(SectionView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        // Get and set section (for controllers of page modules only)
        $section_id = false;
        if ($this->request()->getGet('section_id')) {
            $section_id = $this->request()->getGet('section_id');
        } elseif ($this->request()->getPost('section_id')) {
            $section_id = $this->request()->getPost('section_id');
        } elseif (isset($this->args['section_id'])) {
            $section_id = $this->args['section_id'];
        }

        $this->section = SectionModel::findById($section_id);
        if ($this->section) {
            $page = $this->section->page()->fetch();
            $module = $this->section->module()->fetch();
            $block = $this->section->block()->fetch();

            // Set title and breadcrumb for view
            $this->view
                ->setTitle($module->name)
                ->setSubtitle('ID: ' . $this->section->id() . ' ' . translate('Block') . ': ' . ($block ? $block->title : translate('Not specified')))
                ->addBreadcrumb(translate('Page', [], true), generate_url('backend_page_index', ['language_id' => $page->language_id]))
                ->addBreadcrumb($page->title, generate_url('backend_section_index', ['page_id' => $page->id()]));

            // Set back and preview url
            $this->view
                ->setBackRoute('backend_section_index', ['page_id' => $page->id()])
                ->setPreviewUrl($page->getUrl() . '#section-' . $this->section->id());
        } else {
            throw new RuntimeException('Section not found in request params (GET/POST)');
        }
    }

    /**
     * Render view as content of response.
     *
     * @param string   $viewFile
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    protected function render(string $viewFile, array $parameters = [], Response $response = null): Response
    {
        $module = $this->section->module()->fetch();
        $page = $this->section->page()->fetch();

        $parameters = array_merge([
            'section' => $this->section,
            'page' => $page,
            'module' => $module,], $parameters);

        $this->view->renderView($viewFile, $parameters, 'backend-page-module-view');

        return parent::render('backend/section/content', $parameters, $response);
    }

    /**
     * Update modified when of page.
     *
     * @return bool
     */
    protected function updateModifiedWhen(): bool
    {
        $page = $this->section->page()->fetch();
        $page->updateModifiedWhen();

        return true;
    }
}
