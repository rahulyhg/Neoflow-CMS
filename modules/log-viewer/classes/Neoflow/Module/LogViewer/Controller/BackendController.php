<?php

namespace Neoflow\Module\LogViewer\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\HTTP\Exception\NotFoundException;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\HTTP\Responsing\StreamResponse;

class BackendController extends AbstractToolModuleController
{
    /**
     * Constructor.
     *
     * @param BackendView $view Backend view
     * @param array       $args Route arguments
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $module = ModuleModel::findByColumn('identifier', 'log-viewer');

        $this->engine()->addJavascriptUrl($module->getUrl('/statics/log-viewer.js'));

        $this->view->setTitle('Log Viewer');
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $logfileFolder = new Folder($this->logger()->getLogfileFolderPath());
        $logfiles = $logfileFolder->find('*.'.$this->config()->get('logger')->get('extension'));

        return $this->render('module/log-viewer/index', [
            'logfiles' => $logfiles,
        ]);
    }

    /**
     * Show action.
     *
     * @return Response
     */
    public function showAction(): Response
    {
        $logfile = $this->logger()->getLogfileFolderPath().basename($this->args['logfile']);

        if (is_file($logfile)) {
            // Set back url
            $this->view->setBackRoute('tmod_log_viewer_backend_index');

            // Set title and breadcrumb
            $this->view
                ->setTitle(basename($logfile))
                ->addBreadcrumb('Log Viewer', generate_url('tmod_log_viewer_backend_index'));

            return $this->render('module/log-viewer/show', [
                'logfile' => $this->args['logfile'],
            ]);
        }

        $this->service('alert')->danger(translate('Log file "{0}" not found', [basename($logfile)]));

        return $this->redirectToRoute('tmod_log_viewer_backend_index');
    }

    /**
     * Show action.
     *
     * @return Response
     *
     * @throws NotFoundException
     */
    public function getAction(): Response
    {
        $logfilePath = $this->logger()->getLogfileFolderPath().basename($this->args['logfile']);

        if (is_file($logfilePath)) {
            $logfile = new File($logfilePath);

            return new StreamResponse($logfile);
        }

        throw new NotFoundException();
    }
}
