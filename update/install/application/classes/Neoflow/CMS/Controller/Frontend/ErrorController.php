<?php

namespace Neoflow\CMS\Controller\Frontend;

use Exception;
use Neoflow\CMS\Controller\FrontendController;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\HTTP\Exception\BadRequestException;
use Neoflow\Framework\HTTP\Exception\ForbiddenException;
use Neoflow\Framework\HTTP\Exception\NotFoundException;
use Neoflow\Framework\HTTP\Exception\UnauthorizedException;
use Neoflow\Framework\HTTP\Responsing\Response;
use Throwable;

class ErrorController extends FrontendController
{
    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * Constructor.
     *
     * @param FrontendView $view
     * @param array        $args
     */
    public function __construct(FrontendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        // Reset application
        $this->view->engine()->unsetBlocks();
        $this->app()->delete('page');
        $this->app()->delete('page_url');
        $this->app()->delete('module_url');
        $this->app()->delete('module_url_routed');

        // Set exception or set unknown exception when there is no exception as argument set
        if (isset($this->args['exception']) && $this->args['exception'] instanceof Throwable) {
            $this->exception = $this->args['exception'];
        } else {
            $this->exception = new Exception('Unknown error');
        }
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        switch (true) {
            case $this->exception instanceof NotFoundException:
                return $this->notFoundAction();
            case $this->exception instanceof BadRequestException:
                return $this->badRequestAction();
            case $this->exception instanceof ForbiddenException:
                return $this->forbiddenAction();
            case $this->exception instanceof UnauthorizedException:
                return $this->unauthorizedAction();
            default:
                return $this->internalServerErrorAction();
        }
    }

    /**
     * Bad request action.
     *
     * @return Response
     */
    public function badRequestAction(): Response
    {
        $this->view->setTitle(translate('Bad request'));

        return $this->render('frontend/error/bad-request')->setStatusCode(400);
    }

    /**
     * Unauthorized action.
     *
     * @return Response
     */
    public function unauthorizedAction(): Response
    {
        $this->view->setTitle(translate('Unauthorized'));

        return $this->render('frontend/error/unauthorized')->setStatusCode(401);
    }

    /**
     * Forbidden action.
     *
     * @return Response
     */
    public function forbiddenAction(): Response
    {
        $this->view->setTitle(translate('Forbidden'));

        return $this->render('frontend/error/forbidden')->setStatusCode(403);
    }

    /**
     * Not found action.
     *
     * @return Response
     */
    public function notFoundAction(): Response
    {
        $this->view->setTitle(translate('Not found'));

        return $this->render('frontend/error/not-found')->setStatusCode(404);
    }

    /**
     * Error action.
     *
     * @return Response
     */
    public function internalServerErrorAction(): Response
    {
        $exception = false;

        if ($this->settings()->show_error_details) {
            if ($this->exception instanceof Throwable) {
                $exception = $this->exception;
            }
        }

        $this->view->setTitle(translate('Internal server error'));

        return $this->render('frontend/error/internal-server-error', [
                    'exception' => $exception,
                ])
                ->setStatusCode(500);
    }
}
