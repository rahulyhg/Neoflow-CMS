<?php

namespace Neoflow\Module\WYSIWYG\Controller;

use Neoflow\CMS\Controller\Frontend\AbstractPageModuleController;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\WYSIWYG\Model;

class FrontendController extends AbstractPageModuleController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $wysiwyg = Model::findByColumn('section_id', $this->section->id());

        return $this->render('wysiwyg/frontend/index', [
            'wysiwyg' => $wysiwyg,
        ]);
    }

    /**
     * Index action.
     *
     * @todo Remove this method when blog module is finished
     *
     * @return Response
     */
    public function FischAction(): Response
    {
        //$wysiwyg = Model::findByColumn('section_id', $this->section->id());

        return $this->render('wysiwyg/fisch', [
            'wysiwyg' => 'lol',
            'args' => $this->args,
        ]);
    }
}
