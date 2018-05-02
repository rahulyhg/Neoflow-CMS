<?php

namespace Neoflow\Framework\Handler;

use Neoflow\CMS\Core\AbstractView;
use Neoflow\Framework\AppTrait;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use OutOfRangeException;
use RuntimeException;

class Router
{
    /**
     * App trait.
     */
    use AppTrait;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $currentRouting = [];

    /**
     * @var string
     */
    protected $routeUrlRegexPattern = '/\(([a-zA-Z0-9\-\_]+)\:(any|num|string|uri)\)/';

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Load route file
        $this->loadRouteFile($this->config()->getApplicationPath('/routes.php'));

        $this->logger()->debug('Router created');
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
    public function addRoutes(array $routes, string $namespace = '', string $prefix = ''): self
    {
        if (!is_array($routes[0])) {
            $routes = [$routes];
        }

        if ($namespace || $prefix) {
            foreach ($routes as $route) {
                if (4 === count($route)) {
                    $route = array_values($route);
                    $route[0] = $prefix.$route[0];
                    $route[3] = $namespace.$route[3];
                    $this->routes[] = $route;
                }
            }
        } else {
            $this->routes = array_merge($this->routes, $routes);
        }

        return $this;
    }

    /**
     * Load route file.
     *
     * @param string $routeFilePath Route file path
     * @param bool   $silent        Set TRUE to prevent an error when route file path doesn't exist
     *
     * @return self
     *
     * @throws RuntimeException
     */
    protected function loadRouteFile(string $routeFilePath, bool $silent = false): self
    {
        if (is_file($routeFilePath)) {
            $routesData = include $routeFilePath;
            if (isset($routesData['routes'])) {
                $routesData = [$routesData];
            }
            foreach ($routesData as $routes) {
                $namespace = '';
                if (isset($routes['namespace'])) {
                    $namespace = $routes['namespace'];
                }

                $prefix = '';
                if (isset($routes['prefix'])) {
                    $prefix = $routes['prefix'];
                }

                $this->addRoutes($routes['routes'], $namespace, $prefix);
            }

            $this->logger()->debug('Route file loaded', [
                'File' => $routeFilePath,
            ]);
        } elseif (!$silent) {
            throw new RuntimeException('Route file "'.$routeFilePath.'" not found');
        }

        return $this;
    }

    /**
     * Get routing by URL and optional HTTP method.
     *
     * @param string $urlPath    URL path of request
     * @param string $httpMethod HTTP method of request
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getRoutingByUrl(string $urlPath, string $httpMethod = 'any'): array
    {
        // Generate cache key
        $cacheKey = sha1($httpMethod.$urlPath);

        // Get from cachen when cache key exists
        if ($this->cache()->exists($cacheKey)) {
            return $this->cache()->fetch($cacheKey);
        } else {
            foreach ($this->routes as $route) {
                $args = [];
                $routeMethods = explode('|', $route[1]);
                $routeUrlPath = $route[2];

                if ($routeUrlPath && ('any' === mb_strtolower($routeMethods[0]) || mb_strtolower($routeMethods[0]) === mb_strtolower($httpMethod))) {
                    // Get args of routeUrl
                    $routeUrlArgs = $this->getRouteUrlArgs($routeUrlPath);

                    // Create regexCode of routeUrl
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[string|any]+\)/ ', '([a-zA-Z0-9\-\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\<\+\,\;\=]+)', $routeUrlPath);
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[num]+\)/', '([0-9\.\,]+)', $routeUrlRegex);
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[uri]+\)/', '(.*)', $routeUrlRegex);
                    $routeUrlRegex = str_replace(['/'], ['\/'], $routeUrlRegex);
                    $routeUrlRegex = '/^'.$routeUrlRegex.'$/';

                    // Remove args from route URL path
                    $routeUrlPath = str_replace('//', '/', preg_replace($this->routeUrlRegexPattern, '', $routeUrlPath));

                    // Check if route URL path (regexCode) is matching uri
                    if (preg_match($routeUrlRegex, $urlPath)) {
                        $urlParts = array_values(array_filter(explode('/', $urlPath)));
                        $routeUrlParts = array_values(array_filter(explode('/', $route[2])));

                        foreach ($urlParts as $index => $urlPart) {
                            if (preg_match($this->routeUrlRegexPattern, $routeUrlParts[$index], $routeUrlArgs)) {
                                if ('num' === $routeUrlArgs[2] && is_numeric($urlPart)) {
                                    if (is_float($urlParts)) {
                                        $args[$routeUrlArgs[1]] = (float) $urlPart;
                                    } else {
                                        $args[$routeUrlArgs[1]] = (int) $urlPart;
                                    }
                                } elseif ('string' === $routeUrlArgs[2] && is_string($urlPart)) {
                                    $args[$routeUrlArgs[1]] = $urlPart;
                                } elseif ('uri' === $routeUrlArgs[2]) {
                                    $args[$routeUrlArgs[1]] = $urlPath;
                                    break;
                                } elseif ('any' === $routeUrlArgs[2]) {
                                    $args[$routeUrlArgs[1]] = $urlPart;
                                }
                            }
                        }

                        // Create routing
                        $routing = [
                            'route' => $route,
                            'args' => $args,
                        ];

                        // Set to cache
                        $this->cache()->store($cacheKey, $routing, 0, ['system-configurations']);

                        return $routing;
                    }
                }
            }
        }
        throw new OutOfRangeException('Route not found (URL path: '.$urlPath.')');
    }

    /**
     * Start routing.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function execute(): Response
    {
        $urlPath = $this->request()->getUrlPath();
        $httpMethod = $this->request()->getHttpMethod();

        // Remove end-slash from URL
        if (mb_strlen($urlPath) > 1 && '/' === mb_substr($urlPath, -1)) {
            $urlPath = rtrim($urlPath, '/');

            return new RedirectResponse($this->config()->getUrl($urlPath));
        }

        // Get routing
        $routing = $this->getRoutingByUrl($urlPath, $httpMethod);

        // Check whether language code not found, not sent if needed or sent if not needed
        $urlLanguageCode = $this->request()->getUrlLanguage();
        $languageCodes = $this->translator()->getLanguageCodes();
        if (($urlLanguageCode && count($languageCodes) && !in_array($urlLanguageCode, $languageCodes)) ||
            (count($languageCodes) > 1 && !$urlLanguageCode) ||
            (1 === count($languageCodes) && $urlLanguageCode)) {
            $url = $this->generateUrl($routing['route'][0], array_merge($routing['args'], $this->request()->getGetData()->toArray()));

            return new RedirectResponse($url);
        }

        $this->logger()->debug('Router executed');

        return $this->route($routing['route'], $routing['args']);
    }

    /**
     * Get route by key.
     *
     * @param string $key Route key
     *
     * @return array
     *
     * @throws OutOfRangeException
     */
    public function getRouteByKey(string $key): array
    {
        foreach ($this->routes as $index => $route) {
            if ($route[0] === $key) {
                return $this->routes[$index];
            }
        }

        throw new OutOfRangeException('Route not found (Key: '.$key.')');
    }

    /**
     * Route to controller and action with key of route.
     *
     * @param string       $routeKey
     * @param array        $args
     * @param AbstractView $view
     *
     * @return Response
     */
    public function routeByKey($routeKey, array $args, AbstractView $view = null): Response
    {
        $route = $this->getRouteByKey($routeKey);

        return $this->route($route, $args, $view);
    }

    /**
     * Route to controller and action.
     *
     * @param array        $route
     * @param array        $args
     * @param AbstractView $view
     *
     * @throws RuntimeException
     *
     * @return Response
     */
    public function route(array $route, array $args = [], AbstractView $view = null): Response
    {
        $this->currentRouting = [
            'route' => $route,
            'args' => $args,
        ];

        $routePathParts = $this->getRoutePathParts($route[3]);

        $controllerClass = $routePathParts['controllerClass'];
        $actionMethod = $routePathParts['actionMethod'];

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass($view, $args);

            // Set current controller
            $this->app()->set('controller', $controller);

            if (method_exists($controller, $actionMethod)) {
                $response = $controller->preHook();
                if ('' === $response->getContent() && 0 === count($response->getHeaders())) {
                    $response = $controller->$actionMethod();
                    $response = $controller->postHook($response);
                }
                $this->logger()->info('Controller and action routed', [
                    'Route' => $route,
                    'Controller' => $controllerClass,
                    'Action' => $actionMethod,
                    'Arguments' => $args,
                ]);

                return $response;
            }
            throw new RuntimeException('Action method "'.$routePathParts['controllerClass'].'@'.$routePathParts['actionMethod'].'" for route "'.$route[0].'" not found');
        }

        throw new RuntimeException('Controller class "'.$routePathParts['controllerClass'].'" for route "'.$route[0].'" not found');
    }

    /**
     * Get current routing.
     *
     * @param string $key Routing key (route or args)
     *
     * @return mixed
     */
    public function getCurrentRouting(string $key = '')
    {
        if (isset($this->currentRouting[$key])) {
            return $this->currentRouting[$key];
        }

        return $this->currentRouting;
    }

    /**
     * Check whether route is active.
     *
     * @param mixed $keys Route keys
     *
     * @return bool
     */
    public function isCurrentRoute($keys): bool
    {
        $currentRoute = $this->getCurrentRouting('route');

        if (isset($currentRoute[0])) {
            $currentRouteKey = $currentRoute[0];

            if (is_string($keys)) {
                $keys = [$keys];
            }

            foreach ($keys as $routeKey) {
                if (fnmatch($routeKey, $currentRouteKey)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the controller and action name of the route path.
     *
     * @param string $routePath           Route path
     * @param string $defaultActionMethod Default action method
     *
     * @return array
     */
    protected function getRoutePathParts(string $routePath, string $defaultActionMethod = 'indexAction'): array
    {
        $routePathParts = explode('@', $routePath);
        $result = [
            'controllerClass' => $routePathParts[0].'Controller',
            'actionMethod' => $defaultActionMethod,
        ];
        if (isset($routePathParts[1])) {
            $result['actionMethod'] = $routePathParts[1].'Action';
        }

        return $result;
    }

    /**
     * Generate URL of route.
     *
     * @param string $key          Route key
     * @param array  $args         URL path arguments
     * @param array  $parameters   URL query parameters
     * @param string $languageCode URL language code
     *
     * @return string
     */
    public function generateUrl(string $key, array $args = [], array $parameters = [], string $languageCode = ''): string
    {
        $routeUrlPath = $key;
        if ($key) {
            $route = $this->getRouteByKey($key);
        } else {
            $route = $this->getCurrentRouting('route');
            $args = $this->getCurrentRouting('args');
        }
        if ($route) {
            $routeUrlPath = $route[2];

            if (count($args)) {
                $routeUrlArgs = $this->getRouteUrlArgs($routeUrlPath);

                if (isset($routeUrlArgs[1])) {
                    for ($i = 0; $i < count($routeUrlArgs[1]); ++$i) {
                        if (isset($args[$routeUrlArgs[1][$i]])) {
                            $pattern = '/\(['.preg_quote($routeUrlArgs[1][$i]).']+\:[any|num|string|uri]+\)/';
                            $routeUrlPath = preg_replace($pattern, $args[$routeUrlArgs[1][$i]], $routeUrlPath);
                            unset($args[$routeUrlArgs[1][$i]]);
                        }
                    }
                }

                if (isset($args['slug'])) {
                    $routeUrlPath .= $args['slug'];
                    unset($args['slug']);
                }

                if (count($args)) {
                    $parameters = array_merge($parameters, $args);
                }
            }

            $pattern = '/(\/\([a-zA-Z0-9]+\:[any|num|string|uri]+\))/';
            $routeUrlPath = preg_replace($pattern, '', $routeUrlPath);
        }

        if ($languageCode) {
            $routeUrlPath = '/'.$languageCode.$routeUrlPath;
        } elseif (count($this->config()->get('app')->get('languages')) > 1) {
            $routeUrlPath = '/'.$this->translator()->getCurrentLanguageCode().$routeUrlPath;
        }

        $parameters = array_filter($parameters);
        if (is_array($parameters) && count($parameters)) {
            $routeUrlPath .= '?'.http_build_query($parameters);
        }

        return $this->config()->getUrl($routeUrlPath);
    }

    /**
     * Get route URL arguments from route URL path.
     *
     * @param string $routeUrlPath Route URL path
     *
     * @return array
     */
    protected function getRouteUrlArgs(string $routeUrlPath): array
    {
        preg_match_all($this->routeUrlRegexPattern, $routeUrlPath, $matches);

        return $matches;
    }
}
