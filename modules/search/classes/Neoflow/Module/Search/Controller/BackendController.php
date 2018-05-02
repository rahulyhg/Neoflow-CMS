<?php
namespace Neoflow\Module\Search\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Sitemap\Model\SettingModel;
use RuntimeException;
use function translate;

class BackendController extends AbstractToolModuleController
{

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     *
     * @throws RuntimeException
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view->setTitle('Search');
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $settings = SettingModel::findById(1);

        return $this->render('/search/backend/index', [
                'settings' => $settings,
        ]);
    }
}
