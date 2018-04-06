<?php
namespace Neoflow\Module\Code\Controller;

use Neoflow\CMS\Controller\Backend\AbstractPageModuleController;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use Neoflow\Module\Code\Model;
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
        // Get code
        $code = Model::findByColumn('section_id', $this->section->id());

        // Set code status

        try {
            $codeStatusMessage = translate('Code is valid and executable');
            $codeStatus = true;
            $code->validateCode();
        } catch (ValidationException $ex) {
            $codeStatus = false;
            $codeStatusMessage = $ex->getMessage();
        }

        return $this->render('code/backend', [
                'codeStatus' => $codeStatus,
                'codeStatusMessage' => $codeStatusMessage,
                'code' => $code,
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

            // Update code content by id
            $code = Model::updateById([
                    'content' => $postData->get('content')->get('section-' . $section_id),
                    ], $postData->get('code_id'));

            // Validate and save code content
            if ($code && $code->save() && $this->updateModifiedWhen()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Update code failed (ID: ' . $postData->get('code_id') . ')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert($ex->getErrors());
        }

        return $this->redirectToRoute('pmod_code_backend_index', [
                'section_id' => $code->section_id
        ]);
    }
}
