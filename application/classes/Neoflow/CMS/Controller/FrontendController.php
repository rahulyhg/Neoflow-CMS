<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\Model\PageModel;
use Neoflow\CMS\View\FrontendView;
use Neoflow\Framework\HTTP\Exception\ForbiddenException;
use Neoflow\Framework\HTTP\Exception\NotFoundException;
use Neoflow\Framework\HTTP\Exception\UnauthorizedException;
use Neoflow\Framework\HTTP\Responsing\Response;

class FrontendController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param FrontendView $view
     * @param array        $args
     */
    public function __construct(FrontendView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new FrontendView();
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
        if (isset($this->args['url'])) {
            $page = PageModel::repo()
                ->where('url', '=', $this->args['url'])
                ->where('language_id', '=', $this->translator()->getActiveLanguage()->id())
                ->fetch();

            if ($page && $page->is_startpage && mb_strlen($this->args['url']) > 1) {
                return $this->redirect($this->config()->getUrl());
            }
        } else {
            $page = PageModel::repo()
                ->where('is_startpage', '=', true)
                ->where('language_id', '=', $this->translator()->getActiveLanguage()->id())
                ->fetch();
        }

        if ($page) {
            $page->isReadOnly();

            // Check whether page is accessible
            if ($page->isAccessible()) {
                $this->app()->set('page', $page);

                return $this->render('page');
            }

            // Throw unauthorized exception when user is authenticated
            if ($this->getService('auth')->getUser()) {
                throw new UnauthorizedException();
            }
            throw new ForbiddenException();
        } elseif (isset($this->args['url']) && (count(explode('/', $this->args['url'])) > 1)) {
            // Define URL paths
            $moduleUrlPath = mb_substr($this->args['url'], mb_strrpos($this->args['url'], '/')).$this->app()->get('module_url');
            $pageUrlPath = mb_substr($this->args['url'], 0, mb_strrpos($this->args['url'], '/'));

            // Set URL paths as app params
            $this->app()->set('module_url', $moduleUrlPath);
            $this->app()->set('page_url', $pageUrlPath);

            // Set routing status of module url
            $this->app()->set('module_url_routed', false);

            // Update URL as argument with new page URL path
            $this->args['url'] = $this->app()->get('page_url');

            // Recall index action
            return $this->indexAction();
        }
        throw new NotFoundException();
    }
}
