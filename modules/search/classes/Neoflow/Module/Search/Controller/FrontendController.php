<?php

namespace Neoflow\Module\Search\Controller;

use Neoflow\CMS\Controller\FrontendController as CmsFrontendController;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Search\Results;
use function translate;

class FrontendController extends CmsFrontendController
{
    /**
     * Constructor.
     *
     * @param FrontendView $view Frontend view
     * @param array        $args Route arguments
     */
    public function __construct(FrontendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view->setTitle(translate('Search'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $query = $this->request()->getGet('q');

        $results = new Results();

        if ($query) {
            $this->view->setTitle(translate('Search results for "{0}"', [$query]));

            $results = $this->service('search')->search($query);
        }

        return $this->render('search/frontend/index', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
