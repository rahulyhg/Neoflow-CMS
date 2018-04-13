<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\Response;

class BackendController extends AbstractController
{
    /**
     * @var array
     */
    protected $permissionKeys = [];

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new BackendView();
        }

        parent::__construct($view, $args);
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->redirectToRoute('backend_dashboard_index');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return true;
    }

    /**
     * Pre hook.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        $currentRoute = $this->router()->getCurrentRouting('route');

        $anonymousRoutes = [
            'backend_unauthorized',
            'backend_auth_login',
            'backend_auth_authenticate',
            'backend_auth_lost_password',
            'backend_auth_reset_password',
            'backend_auth_new_password',
            'backend_auth_update_password',
        ];

        if ($this->getService('auth')->isAuthenticated()) {
            if (in_array($currentRoute[0], $anonymousRoutes)) {
                return $this->redirectToRoute('backend_dashboard_index');
            } elseif (!$this->checkPermission()) {
                return $this->route('error_unauthorized');
            }
        } else {
            if (!in_array($currentRoute[0], $anonymousRoutes)) {
                $args = [];
                if ($this->request()->isHttpMethod('GET')) {
                    $args['url'] = $this->request()->getUrlPath(true, true);
                }

                return $this->redirectToRoute('backend_auth_login', $args);
            }
        }

        return new Response();
    }
}
