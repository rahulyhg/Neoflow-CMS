<?php
namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
use Neoflow\Framework\Handler\Router as FrameworkRouter;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Throwable;

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

// Load route of application
            $routeFilePath = $this->config()->getApplicationPath('/routes.php');
            $this->loadRouteFile($routeFilePath);

// Add frontend index route to the end of the array
            $this->addRoute(array('frontend_index', 'any', '/(url:uri)', 'Frontend@index'), '\\Neoflow\\CMS\\Controller\\');

// Store template and view file directories to cache
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
//        try {
        $urlPath = $this->request()->getUrlPath();

        if (!$this->app()->get('database') && false === strpos($urlPath, '/install')) {
            $url = $this->generateUrl('install_index');

            return new RedirectResponse($url);
        }

        return parent::execute();
//        } catch (Throwable $ex) {
//            while (ob_get_level() > 1) {
//                ob_end_clean();
//            }
//
//            $this->logger()->logException($ex);
//
//            // Route only if database connection is etablished
//            if ($this->app()->get('database')) {
//                return $this->routeByKey('error_index', ['exception' => $ex]);
//            }
//            throw $ex;
//}
    }
}
