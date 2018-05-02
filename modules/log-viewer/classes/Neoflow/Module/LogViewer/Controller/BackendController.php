<?php
namespace Neoflow\Module\LogViewer\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Exception\NotFoundException;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Framework\HTTP\Responsing\StreamResponse;
use Neoflow\Filesystem\File;
use Neoflow\Filesystem\Folder;

class BackendController extends AbstractToolModuleController
{

    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $module = \Neoflow\CMS\Model\ModuleModel::findByColumn('identifier', 'log-viewer');

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
        $logfiles = $logfileFolder->find('*.' . $this->config()->get('logger')->get('extension'));

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
        $logfile = $this->logger()->getLogfileFolderPath() . basename($this->args['logfile']);

        if (is_file($logfile)) {
            // Set back url
            $this->view->setBackRoute('tmod_log_viewer_backend_index');

            // Set title and breadcrumb
            $this->view
                ->setTitle(basename($logfile))
                ->addBreadcrumb(translate('Log Viewer'), generate_url('tmod_log_viewer_backend_index'));

            return $this->render('module/log-viewer/show', [
                    'logfile' => $this->args['logfile'],
            ]);
        }

        $this->view->setDangerAlert(translate('Log file "{0}" not found', [basename($logfile)]));

        return $this->redirectToRoute('tmod_log_viewer_backend_index');
    }

    /**
     * Show action.
     *
     * @return Response
     */
    public function getAction(): Response
    {
        $logfilePath = $this->logger()->getLogfileFolderPath() . basename($this->args['logfile']);

        if (is_file($logfilePath)) {
            $logfile = new File($logfilePath);

            return new StreamResponse($logfile);
        }

        return new NotFoundException();
    }
}
