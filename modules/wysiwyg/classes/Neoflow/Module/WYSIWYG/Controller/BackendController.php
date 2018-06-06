<?php

namespace Neoflow\Module\WYSIWYG\Controller;

use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Module\WYSIWYG\Model;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class BackendController extends AbstractPageModuleController
{
    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        // Get wysiwyg content
        $wysiwyg = Model::findByColumn('section_id', $this->section->id());

        return $this->render('wysiwyg/backend', [
                'wysiwyg' => $wysiwyg,
        ]);
    }

    /**
     * Update action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Get section id from post data
            $section_id = $postData->get('section_id');

            // Update wysiwyg content by id
            $wysiwyg = Model::updateById([
                    'content' => $postData->get('content')->get('section-'.$section_id),
                    ], $postData->get('wysiwyg_id'));

            // Validate and save wysiwyg content
            if ($wysiwyg && $wysiwyg->save() && $this->updateModifiedWhen()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Update wysiwyg content failed (ID: '.$postData->get('editor_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('pmod_wysiwyg_backend_index', [
                'section_id' => $wysiwyg->section_id,
        ]);
    }
}
