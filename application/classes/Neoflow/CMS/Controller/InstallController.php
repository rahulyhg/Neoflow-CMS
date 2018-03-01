<?php
namespace Neoflow\CMS\Controller;

use Neoflow\CMS\Core\AbstractController;
use Neoflow\CMS\Model\SettingModel;
use Neoflow\CMS\Service\InstallService;
use Neoflow\CMS\View\InstallView;
use Neoflow\Framework\HTTP\Responsing\Response;

class InstallController extends AbstractController
{

    /**
     * @var InstallService
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param InstallView $view
     * @param array       $args
     */
    public function __construct(InstallView $view = null, array $args = [])
    {
        if (!$view) {
            $view = new InstallView();
        }

        parent::__construct($view, $args);

        // Set title and website title
        $this->view
            ->setTitle(translate('Installation'))
            ->setWebsiteTitle('Neoflow CMS')
            ->set('brandTitle', translate('Installation'));

        // Create install service
        $this->service = new InstallService();
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
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
        $installPath = $this->config()->getPath('/install');
        // TODO: Uncomment "unlink operation" when go live
        //Folder::unlink($installPath, true);

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
        $installPath = $this->config()->getPath('/install');
        if (!is_dir($installPath)) {
            return $this->redirectToRoute('frontend_index');
        }

        return parent::preHook();
    }
}
