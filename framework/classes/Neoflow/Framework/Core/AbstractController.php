<?php

namespace Neoflow\Framework\Core;

use Neoflow\Framework\AppTrait;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\HTTP\Session;
use RuntimeException;

abstract class AbstractController
{
    /**
     * @var AbstractView
     */
    protected $view;

    /**
     * @var array
     */
    protected $args = [];

    /**
     * App trait.
     */
    use AppTrait;

    /**
     * Constructor.
     *
     * @param AbstractView $view
     * @param array        $args
     */
    public function __construct(AbstractView $view = null, array $args = [])
    {
        if ($view) {
            $this->view = $view;
            $this->app()->set('view', $view);
        }

        $this->args = $args;

        $this->logger()->info('Controller created', [
            'Type' => $this->getReflection()->getShortName(),
        ]);
    }

    /**
     * Render view as content of response.
     *
     * @param string   $viewFile
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    protected function render(string $viewFile, array $parameters = [], Response $response = null): Response
    {
        if ($this->view instanceof AbstractView) {
            // Overwrite current view
            $this->app()->set('view', $this->view);

            $this->view->renderView($viewFile, $parameters);
            $content = $this->view->renderTheme();

            if (null === $response) {
                $response = new Response();
            }

            return $response->setContent($content);
        }
        throw new RuntimeException('View not found. Check that a view for the controller is set');
    }

    /**
     * Route to controller and action by route key.
     *
     * @param string $routeKey Route key
     * @param array  $args     Controller arguments
     *
     * @return Response
     */
    protected function route(string $routeKey, array $args = []): Response
    {
        return $this->router()->routeByKey($routeKey, $args);
    }

    /**
     * Redirect to route.
     *
     * @param string $routeKey   Route key
     * @param array  $args       HTPP query data
     * @param int    $statusCode HTTP status code
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $routeKey, array $args = [], int $statusCode = 302): RedirectResponse
    {
        return $this->redirect(generate_url($routeKey, $args), $statusCode);
    }

    /**
     * Redirect to url.
     *
     * @param string $url        URL
     * @param int    $statusCode HTTP status code
     *
     * @return RedirectResponse
     */
    public function redirect(string $url, int $statusCode = 302): RedirectResponse
    {
        return new RedirectResponse($url, $statusCode);
    }

    /**
     * Pre hook method.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        return new Response();
    }

    /**
     * Post hook method.
     *
     * @param Response $response Response
     *
     * @return Response
     */
    public function postHook(Response $response)
    {
        return $response;
    }

    /**
     * Set new session flash value.
     *
     * @param string $key   Flash key
     * @param mixed  $value Flash value
     *
     * @return Session
     */
    protected function setNewFlash(string $key, $value)
    {
        return $this->session()->setNewFlash($key, $value);
    }
}
