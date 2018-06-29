<?php

namespace Neoflow\Module\Blog\Controller;

use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\CMS\View\Backend\SectionView;

class BackendController extends AbstractPageModuleController
{
    /**
     * Constructor.
     *
     * @param SectionView $view Section view
     * @param array       $args Request arguments
     */
    public function __construct(SectionView $view = null, array $args = [])
    {
        parent::__construct($view, $args);
    }
}
