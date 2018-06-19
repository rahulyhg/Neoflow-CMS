<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\View\BackendView;

abstract class AbstractToolModuleController extends BackendController
{
    /**
     * Constructor.
     *
     * @param BackendView $view Backend view
     * @param array       $args Request arguments
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        // Set title and breadcrumb for view
        $this->view->addBreadcrumb(translate('Tool', [], true));

        // Set back url
        $this->view->setBackRoute('backend_tool_index');
    }
}
