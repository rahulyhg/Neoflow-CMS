<?php
namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Service\UpdateService;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use Exception;

class MaintenanceController extends BackendController
{

    /**
     * Constructor.
     *
     * @param BackendView $view
     * @param array       $args
     */
    public function __construct(BackendView $view = null, array $args = [])
    {
        parent::__construct($view, $args);

        $this->view
            ->setTitle(translate('Maintenance'));
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('maintenance');
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $visitorStats = [
            'current' => $this->getService('stats')->getNumberOfCurrentVisitors(),
            'today' => $this->getService('stats')->getNumberOfVisitorsToday(),
            'month' => $this->getService('stats')->getNumberOfVisitorsThisMonth(),
            'total' => $this->getService('stats')->getTotalNumberOfVisitors(),
        ];

        return $this->render('backend/maintenance/index', [
                'visitorStats' => $visitorStats,
        ]);
    }

    /**
     * Clear cache action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function clearCacheAction(): RedirectResponse
    {
        $cacheTag = $this->request()->getPost('cache');

        if ($cacheTag) {
            if ('all' === $cacheTag) {
                $this->cache()->clear();
            } else {
                $this->cache()->deleteByTag($cacheTag);
            }
            $this->view->setSuccessAlert(translate('Successfully cleared'));
        } else {
            throw new RuntimeException('Clearing cache failed');
        }

        return $this->redirectToRoute('backend_maintenance_index');
    }

    /**
     * Clear cache action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteLogfilesAction(): RedirectResponse
    {
        $numberOfDays = abs($this->request()->getPost('logfiles')) + 1;

        $logfiles = $this->logger()->getLogfiles();
        $logConfig = $this->config()->get('logger');

        foreach ($logfiles as $logfile) {
            $logfileDate = str_replace($logConfig->get('prefix'), '', basename($logfile->getPath(), '.' . $logConfig->get('extension')));

            if (strtotime($logfileDate) < strtotime('-' . $numberOfDays . ' days')) {
                $logfile->delete();
            }
        }

        $this->view->setSuccessAlert(translate('Successfully deleted'));

        return $this->redirectToRoute('backend_maintenance_index');
    }

    /**
     * Reset visitor stats action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function resetVisitorStatsAction(): RedirectResponse
    {
        $this->getService('stats')->reset();

        $this->view->setSuccessAlert(translate('Successfully reseted'));

        return $this->redirectToRoute('backend_maintenance_index');
    }

    /**
     * Reset folder permissions.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function resetFolderPermissionsAction(): RedirectResponse
    {
        $folders = $this->config()->get('folders');

        foreach ($folders as $folder) {
            $absolutePath = $this->config()->getPath($folder->get('path'));
            chmod($absolutePath, $folder->get('permission'));
        }

        $this->view->setSuccessAlert(translate('Successfully reseted'));

        return $this->redirectToRoute('backend_maintenance_index');
    }

    /**
     * Install update action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function installUpdateAction(): RedirectResponse
    {
        $uploadedItem = $this->request()->getFile('file');

        try {
            $file = $this->getService('upload')->move($uploadedItem, $this->config()->getTempPath(), true, ['zip']);

            $updateService = new UpdateService();
            $updateService->install($file);

            $this->view->setSuccessAlert(translate('CMS successfully updated'));
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update CMS failed'), [$ex->getMessage()]]);
        } catch (Exception $ex) {
            $this->view->setDangerAlert([translate('Update CMS failed, see error message'), [$ex->getMessage()]]);
        }

        return $this->redirectToRoute('backend_maintenance_index')
                ->setHeader('Cache-Control: no-cache')
                ->setHeader('Pragma: no-cache');
    }
}
