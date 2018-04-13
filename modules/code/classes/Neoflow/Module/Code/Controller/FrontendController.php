<?php

namespace Neoflow\Module\Code\Controller;

use Neoflow\CMS\Controller\Frontend\AbstractPageModuleController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\Code\Model;

class FrontendController extends AbstractPageModuleController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $code = Model::findByColumn('section_id', $this->section->id());

        return $this->render('code/frontend', [
                'code' => $code,
        ]);
    }
}
