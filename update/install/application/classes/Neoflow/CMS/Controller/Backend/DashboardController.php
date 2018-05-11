<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\Response;

class DashboardController extends BackendController
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

        // Set title
        $this->view->setTitle(translate('Dashboard'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/dashboard/index', [
                'phpinfo' => phpinfo2array('phpinfo'),
        ]);
    }
}
