<?php
namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\CMS\Model\UserModel;
use Neoflow\Framework\Handler\Router as FrameworkRouter;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;

class Router extends FrameworkRouter
{

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if ($this->cache()->exists('routes')) {
            // Fetch routes from cache
            $this->routes = $this->cache()->fetch('routes');
        } else {
            // Load route file of each module
            if ($this->app()->exists('modules')) {
                $modules = $this->app()->get('modules');
                foreach ($modules as $module) {
                    $routeFilePath = $module->getPath('/routes.php');
                    $this->loadRouteFile($routeFilePath, true);
                }
            }

            // Load route of CMS application
            $routeFilePath = $this->config()->getApplicationPath('/routes.php');
            $this->loadRouteFile($routeFilePath);

            // Add frontend index route to the end of the array
            $this->addRoute(['frontend_index', 'any', '/(url:uri)', 'Frontend@index'], '\\Neoflow\\CMS\\Controller\\');

            // Store routes to cache
            $this->cache()->store('routes', $this->routes, 0, ['system-configurations']);
        }

        $this->logger()->debug('Router created');
    }

    /**
     * Check whether current URL is a valid install URL path.
     *
     * @return boolean
     */
    protected function isInstallUrlPaths(): bool
    {
        // Get valid install url paths
        $installUrlPaths = $this->config()->get('install')->get('urlPaths');

        foreach ($installUrlPaths as $urlPath => $method) {
            if ($this->request()->isUrlPath($urlPath) && $this->request()->isHttpMethod($method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Start routing.
     *
     * @return Response
     */
    public function execute(): Response
    {
        $urlPath = $this->request()->getUrlPath();

        $installUrlPaths = [
            '/install',
            '/install/database',
            '/install/database/create',
            '/install/website',
            '/install/website/create',
            '/install/administrator',
            '/install/administrator/create',
            '/install/success',
        ];

        if (!$this->getService('install')->databaseStatus() && !in_array($urlPath, $installUrlPaths)) {
            $url = $this->generateUrl('install_index');
            return new RedirectResponse($url);
        }

        return parent::execute();
    }
}
