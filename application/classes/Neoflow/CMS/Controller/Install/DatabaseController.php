<?php

namespace Neoflow\CMS\Controller\Install;

use Neoflow\CMS\Controller\InstallController;
use Neoflow\Filesystem\File;
use Neoflow\Framework\HTTP\Responsing\Response;
use Throwable;
use function translate;

class DatabaseController extends InstallController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Get config as array
        $config = $this->config()->toArray();

        // Add customized URL (instead of automatically detected)
        $url = $this->request()->getGet('url');
        if ($url) {
            $config['url'] = $url;
        }

        if ($this->session()->hasFlash('config')) {
            $config = $this->session()->getFlash('config');
        }

        return $this->render('install/database/index', $config);
    }

    /**
     * Create action.
     *
     * @return Response
     */
    public function createAction(): Response
    {
        // Set post data as array
        $config = $this->request()->getPostData()->toArray();

        try {
            // Etablish database connection and create tables
            $this->service('install')->createDatabase($config['database']);

            // Create config file
            $this->service('install')->createConfigFile($config);

            // Update settings
            $this->service('install')->updateSettings();

            $this->service('alert')->success(translate('Database successfully installed'));

            return $this->redirectToRoute('install_website_index');
        } catch (Throwable $ex) {
            File::unlink($this->config()->getPath('/config.php'));
            $this->service('alert')->danger([translate('Install failed'), [$ex->getMessage()]]);
        }

        // Set current config as flash
        $this->setNewFlash('config', $config);

        // Redirect to current step
        return $this->redirectToRoute('install_database_index', [
                'url' => $config['url'],
        ]);
    }

    /**
     * Pre hook method.
     *
     * @return Response
     */
    public function preHook(): Response
    {
        // Redirect to the next installer step
        if ($this->service('install')->databaseStatus()) {
            return $this->redirectToRoute('install_website_index');
        }

        return parent::preHook();
    }
}
