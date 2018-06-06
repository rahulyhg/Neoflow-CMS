<?php

namespace Neoflow\Module\Robots\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Robots\File;
use RuntimeException;

class BackendController extends AbstractToolModuleController
{
    /**
     * @var File
     */
    protected $robotsFile;

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     *
     * @throws \Neoflow\Filesystem\Exception\FileException
     * @throws \Neoflow\Filesystem\Exception\FileException
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view->setTitle('Robots');

        // Set robots file
        $robotsPath = $this->config()->getPath('robots.txt');
        if (file_exists($robotsPath)) {
            $this->robotsFile = new File($robotsPath);
        } else {
            $this->robotsFile = File::create($robotsPath, '');
        }
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $content = '';
        if ($this->robotsFile) {
            $content = $this->robotsFile->getContent();
        }

        return $this->render('/robots/index', [
                'content' => $content,
        ]);
    }

    /**
     * Update robots action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        // Get post data
        $content = $this->request()->getPost('content');

        if ($this->robotsFile->setContent($content)) {
            $this->service('alert')->success(translate('Successfully updated'));
        } else {
            throw new RuntimeException('Update robots.txt failed');
        }

        return $this->redirectToRoute('tmod_robots_backend_index');
    }
}
