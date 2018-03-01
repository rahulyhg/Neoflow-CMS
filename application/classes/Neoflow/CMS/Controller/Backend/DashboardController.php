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
        $visitorStats = [
            'current' => $this->getService('stats')->getNumberOfCurrentVisitors(),
            'today' => $this->getService('stats')->getNumberOfVisitorsToday(),
            'month' => $this->getService('stats')->getNumberOfVisitorsThisMonth(),
            'total' => $this->getService('stats')->getTotalNumberOfVisitors(),
        ];

        return $this->render('backend/dashboard/index', [
                'visitorStats' => $visitorStats,
        ]);
    }
}
