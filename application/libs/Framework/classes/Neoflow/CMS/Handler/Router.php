<?php

namespace Neoflow\CMS\Handler;

use Neoflow\CMS\AppTrait;
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
     * @var bool
     */
    protected $cached = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Check whether routes are already cached
        if ($this->cache()->exists('routes')) {
            $this->cached = false;

            // Fetch routes from cache
            $this->routes = $this->cache()->fetch('routes');
        } else {
            parent::__construct();
        }

        $this->logger()->debug('Router created');
    }

    /**
     * Check whether translation data is cached or not.
     *
     * @return bool
     */
    public function isCached(): bool
    {
        return $this->cached;
    }

    /**
     * Add routes.
     *
     * @param array  $routes    One or multiple route arrays
     * @param string $namespace Namespace for route controller
     * @param string $prefix    Prefix for route identifier
     *
     * @return self
     */
    public function addRoutes(array $routes, string $namespace = '', string $prefix = ''): FrameworkRouter
    {
        parent::addRoutes($routes, $namespace, $prefix);

        $this->cache()->store('routes', $this->routes, 0, ['cms_core', 'cms_router', 'cms_routes']);

        return $this;
    }

    /**
     * Check whether current URL is a valid install URL path.
     *
     * @return bool
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
        $installService = $this->service('install');

        if (!$installService->databaseStatus() && !$installService->isRunning()) {
            $url = $this->generateUrl('install_index');

            return new RedirectResponse($url);
        }

        return parent::execute();
    }
}
