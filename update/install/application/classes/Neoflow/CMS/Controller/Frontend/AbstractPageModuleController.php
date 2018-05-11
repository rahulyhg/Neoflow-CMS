<?php

namespace Neoflow\CMS\Controller\Frontend;

use Neoflow\CMS\Controller\FrontendController;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\Model\SectionModel;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\HTTP\Responsing\Response;
use RuntimeException;

abstract class AbstractPageModuleController extends FrontendController
{
    /**
     * @var SectionModel
     */
    protected $section;

    /**
     * @var PageModel
     */
    protected $page;

    /**
     * Constructor.
     *
     * @param FrontendView $view
     * @param array        $args
     */
    public function __construct(FrontendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        if (isset($this->args['section']) && $this->args['section'] instanceof SectionModel) {
            $this->section = $this->args['section'];
            unset($this->args['section']);
        } else {
            throw new RuntimeException('Section not found');
        }

        $page = $this->app()->get('page');
        if ($page instanceof PageModel) {
            $this->page = $page;
        } else {
            throw new RuntimeException('Page not found');
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
     *
     * @throws RuntimeException
     */
    protected function render(string $viewFile, array $parameters = [], Response $response = null): Response
    {
        $module = $this->section->module()->fetch();

        $parameters = array_merge([
            'section' => $this->section,
            'page' => $this->page,
            'module' => $module, ], $parameters);

        $this->view->renderView($viewFile, $parameters, 'section-content');

        if ($response) {
            return $response;
        }

        return new Response();
    }
}
