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
     * Add single route.
     *
     * @param array  $route
     * @param string $namespace
     * @param string $prefix
     *
     * @return Router
     */
    public function addRoute($route, $namespace = '', $prefix = '')
    {
        if (4 === count($route)) {
            $route = array_values($route);
            $route[0] = $prefix . $route[0];
            $route[3] = $namespace . $route[3];
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * Add routes.
     *
     * @param array  $routes
     * @param string $namespace
     * @param string $prefix
     *
     * @return Router
     */
    public function addRoutes($routes, $namespace = '', $prefix = '')
    {
        if ($namespace) {
            foreach ($routes as $route) {
                $this->addRoute($route, $namespace, $prefix);
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
     * @param bool   $silent        Disable runtime exception when route file path won't exists
     *
     * @return self
     *
     * @throws RuntimeException
     */
    protected function loadRouteFile($routeFilePath, $silent = false)
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
            throw new RuntimeException('Route file "' . $routeFilePath . '" not found');
        }

        return $this;
    }

    /**
     * Get routing by uri and optional HTTP method.
     *
     * @param string $url        URL path of request
     * @param string $httpMethod HTTP method of request
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getRoutingByUrl($url, $httpMethod = 'any'): array
    {
        // Generate cache key
        $cacheKey = sha1($httpMethod . $url);

        // Get from cachen when cache key exists
        if ($this->cache()->exists($cacheKey)) {
            return $this->cache()->fetch($cacheKey);
        } else {
            foreach ($this->routes as $route) {
                $args = [];
                $routeMethods = explode('|', $route[1]);
                $routeUrl = $route[2];

                if ($routeUrl && ('any' === strtolower($routeMethods[0]) || strtolower($routeMethods[0]) === strtolower($httpMethod))) {
                    // Get args of routeUrl
                    $routeUrlArgs = $this->getRouteUrlArgs($routeUrl);

                    // Create regexCode of routeUrl
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[string|any]+\)/ ', '([a-zA-Z0-9\-\.\_\~\:\?\#\[\]\@\!\$\&\'\(\)\*\<\+\,\;\=]+)', $routeUrl);
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[num]+\)/', '([0-9\.\,]+)', $routeUrlRegex);
                    $routeUrlRegex = preg_replace('/\(([a-zA-Z0-9\-\_]+)\:[uri]+\)/', '(.*)', $routeUrlRegex);
                    $routeUrlRegex = str_replace(array('/'), array('\/'), $routeUrlRegex);
                    $routeUrlRegex = '/^' . $routeUrlRegex . '$/';

                    // Remove args of routeUrl
                    $routeUrl = str_replace('//', '/', preg_replace($this->routeUrlRegexPattern, '', $routeUrl));

                    // Check if routeUrl (regexCode) is matching uri
                    if (preg_match($routeUrlRegex, $url)) {
                        $urlParts = array_values(array_filter(explode('/', $url)));
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
                                    $args[$routeUrlArgs[1]] = $url;
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
        throw new OutOfRangeException('Route not found (URL: ' . $url . ')');
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
        $uriLanguageCode = $this->request()->getUrlLanguage();
        $languageCodes = $this->translator()->getLanguageCodes();
        if (($uriLanguageCode && count($languageCodes) && !in_array($uriLanguageCode, $languageCodes)) ||
            (count($languageCodes) > 1 && !$uriLanguageCode) ||
            (1 === count($languageCodes) && $uriLanguageCode)) {
            $url = $this->generateUrl($routing['route'][0], array_merge($routing['args'], $this->request()->getGetData()->toArray()));

            return new RedirectResponse($url);
        }

        $this->logger()->debug('Router executed');

        return $this->route($routing['route'], $routing['args']);
    }

    /**
     * Get route by key.
     *
     * @param string $key
     *
     * @throws OutOfRangeException
     *
     * @return array
     */
    public function getRouteByKey($key)
    {
        foreach ($this->routes as $index => $route) {
            if ($route[0] === $key) {
                return $this->routes[$index];
            }
        }

        throw new OutOfRangeException('Route not found (Key: ' . $key . ')');
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
        $this->currentRouting = array('route' => $route, 'args' => $args);

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
            throw new RuntimeException('Action method "' . $routePathParts['controllerClass'] . '@' . $routePathParts['actionMethod'] . '" for route "' . $route[0] . '" not found');
        }

        throw new RuntimeException('Controller class "' . $routePathParts['controllerClass'] . '" for route "' . $route[0] . '" not found');
    }

    /**
     * Get active route.
     *
     * @return mixed
     */
    public function getCurrentRouting($key = null)
    {
        if (isset($this->currentRouting[$key])) {
            return $this->currentRouting[$key];
        }

        return $this->currentRouting;
    }

    /**
     * Check whether route is active.
     *
     * @param array|string $routeKeys
     *
     * @return mixed
     */
    public function isCurrentRoute($routeKeys)
    {
        $currentRoute = $this->getCurrentRouting('route');

        if (isset($currentRoute[0])) {
            $currentRouteKey = $currentRoute[0];

            if (is_string($routeKeys)) {
                $routeKeys = array($routeKeys);
            }

            foreach ($routeKeys as $routeKey) {
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
     * @param string $routePath
     * @param string $defaultActionMethod
     *
     * @return array
     */
    public function getRoutePathParts($routePath, $defaultActionMethod = 'indexAction')
    {
        $routePathParts = explode('@', $routePath);
        $result = array(
            'controllerClass' => $routePathParts[0] . 'Controller',
            'actionMethod' => $defaultActionMethod,
        );
        if (isset($routePathParts[1])) {
            $result['actionMethod'] = $routePathParts[1] . 'Action';
        }

        return $result;
    }

    /**
     * Generate URL of route.
     *
     * @param string $routeKey
     * @param array  $args
     * @param array  $params
     * @param string $languageCode
     *
     * @return string
     */
    public function generateUrl($routeKey, $args = [], $params = [], $languageCode = '')
    {
        $routeUri = $routeKey;
        if ($routeKey) {
            $route = $this->getRouteByKey($routeKey);
        } else {
            $route = $this->getCurrentRouting('route');
            $args = $this->getCurrentRouting('args');
        }
        if ($route) {
            $routeUri = $route[2];

            if (count($args)) {
                $routeUriArgs = $this->getRouteUrlArgs($routeUri);

                if (isset($routeUriArgs[1])) {
                    for ($i = 0; $i < count($routeUriArgs[1]); ++$i) {
                        if (isset($args[$routeUriArgs[1][$i]])) {
                            $pattern = '/\([' . preg_quote($routeUriArgs[1][$i]) . ']+\:[any|num|string|uri]+\)/';
                            $routeUri = preg_replace($pattern, $args[$routeUriArgs[1][$i]], $routeUri);
                            unset($args[$routeUriArgs[1][$i]]);
                        }
                    }
                }

                if (isset($args['slug'])) {
                    $routeUri .= $args['slug'];
                    unset($args['slug']);
                }

                $args = array_filter($args);
                if (count($args)) {
                    $routeUri .= '?' . http_build_query($args);
                }
            }

            $pattern = '/(\/\([a-zA-Z0-9]+\:[any|num|string|uri]+\))/';
            $routeUri = preg_replace($pattern, '', $routeUri);
        }

        if ($languageCode) {
            $routeUri = '/' . $languageCode . $routeUri;
        } elseif (count($this->config()->get('languages')) > 1) {
            $routeUri = '/' . $this->translator()->getActiveLanguageCode() . $routeUri;
        }

        $params = array_filter($params);
        if (is_array($params) && count($params)) {
            $routeUri .= '?' . http_build_query($params);
        }

        return $this->config()->getUrl($routeUri);
    }

    /**
     * Get route uri matches.
     *
     * @param string $routeUri
     *
     * @return array
     */
    protected function getRouteUrlArgs($routeUri)
    {
        preg_match_all($this->routeUrlRegexPattern, $routeUri, $matches);

        return $matches;
    }
}
