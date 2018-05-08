<?php

namespace Neoflow\CMS\Controller;

use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\View\InstallView;
use Neoflow\Filesystem\Folder;
use Neoflow\Framework\HTTP\Responsing\Response;
use RuntimeException;

class InstallController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param InstallView $view
     * @param array       $args
     */
    public function __construct(InstallView $view = null, array $args = [])
    {
        // Clear cache
        $this->cache()->clear();

        if (!$view) {
            $view = new InstallView();
        }

        parent::__construct($view, $args);

        // Set website area
        $this->app()->set('area', 'install');

        // Set title and website title
        $this->view
            ->setTitle(translate('Installation'))
            ->setWebsiteTitle('Neoflow CMS')
            ->set('brandTitle', translate('Installation'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Clear cache to avoid errors from pre-installations
        $this->cache()->clear();

        // Redirect to the next installer step
        if ($this->app()->get('database') && SettingModel::findById(1)) {
            return $this->redirectToRoute('install_website_index');
        }

        return $this->render('install/index', [
                'url' => $this->config()->getUrl(),
        ]);
    }

    /**
     * Success action.
     *
     * @return Response
     */
    public function successAction(): Response
    {
        $installationPath = $this->config()->getPath('/installation');

        if (APP_MODE !== 'DEV' && is_dir($installationPath)) {
            Folder::unlink($installationPath, true);
        }

        return $this->render('install/success');
    }

    /**
     * Pre hook method.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        // Redirect to frontend when install folder is removed
        $installationPath = $this->config()->getPath('/installation');
        if (!is_dir($installationPath)) {
            if ($this->getService('install')->databaseStatus()) {
                return $this->redirectToRoute('frontend_index');
            }
            throw new RuntimeException('Something went wrong. Connection to database could not be established and installation not possible.');
        }

        return parent::preHook();
    }
}
