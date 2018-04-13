<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\Response;

class ToolController extends BackendController
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

        // Set title and breadcrumb
        $this->view
            ->setTitle(translate('Tool', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/tool/index', [
                'modules' => ModuleModel::findAllByColumns([
                    'type' => 'tool',
                    'is_active' => true,
                ]),
        ]);
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('run_tools');
    }
}
