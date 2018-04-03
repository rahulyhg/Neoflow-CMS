<?php
namespace Neoflow\Module\Dummy\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\Response;
use function translate;

class BackendController extends AbstractToolModuleController
{

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = array())
    {
        parent::__construct($view, $args);

        $this->view->setTitle(translate('Dummy'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('/dummy/index');
    }
}
