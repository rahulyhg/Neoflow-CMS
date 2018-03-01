<?php

namespace Neoflow\Module\Snippets\Controller;

use Neoflow\CMS\Controller\Backend\AbstractToolModuleController;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use Neoflow\Module\Snippets\Model;
use RuntimeException;

class BackendController extends AbstractToolModuleController
{
    public function __construct(BackendView $view = null, array $args = array())
    {
        parent::__construct($view, $args);

        $this->view
            ->setTitle(translate('Snippet', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('/snippet/index', array(
                'snippets' => Model::findAll(),
        ));
    }

    /**
     * Create snippet action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function createAction(): RedirectResponse
    {
        try {
            // Get post data
            $postData = $this->request()->getPostData();

            // Create snippet
            $snippet = Model::create(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'placeholder' => $postData->get('placeholder'),
                    'code' => '',
            ));

            // Validate and save snippet
            if ($snippet && $snippet->validate() && $snippet->save()) {
                $this->view->setSuccessAlert(translate('Successfully created'));
            } else {
                throw new RuntimeException('Create snippet failed');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_snippets_backend_index');
    }

    /**
     * Edit snippet action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get snippet or data if validation has failed
        $snippet = Model::findById($this->args['id']);
        if ($this->getService('validation')->hasError()) {
            $data = $this->getService('validation')->getData();
            $snippet = Model::updateById($data, $data['snippet_id']);
        }

        if ($snippet) {
            // Set back url
            $this->view->setBackRoute('tmod_snippets_backend_index');

            // Set title and breadcrumb
            $this->view
                ->setTitle(basename($snippet->title))
                ->addBreadcrumb('Snippets', generate_url('tmod_snippets_backend_index'));

            // Set back url
            $this->view->setBackRoute('tmod_snippets_backend_index');

            // Set code status
            try {
                $codeStatusMessage = translate('Code is valid and executable');
                $codeStatus = true;
                $snippet->validateCode();
            } catch (ValidationException $ex) {
                $codeStatus = false;
                $codeStatusMessage = $ex->getMessage();
            }

            return $this->render('snippet/edit', [
                    'snippet' => $snippet,
                    'codeStatus' => $codeStatus,
                    'codeStatusMessage' => $codeStatusMessage,
            ]);
        }

        throw new RuntimeException('Code snippet not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update snippet action.
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

            // Update snippet
            $snippet = Model::updateById(array(
                    'title' => $postData->get('title'),
                    'description' => $postData->get('description'),
                    'placeholder' => $postData->get('placeholder'),
                    'code' => $postData->get('code'),
                    ), $postData->get('snippet_id'));

            // Validate and save snippet
            if ($snippet && $snippet->validate() && $snippet->save()) {
                $this->view->setSuccessAlert(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating snippet failed (ID: '.$postData->get('snippet_id').')');
            }
        } catch (ValidationException $ex) {
            $this->view->setWarningAlert([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('tmod_snippets_backend_edit', array('id' => $postData->get('snippet_id')));
    }

    /**
     * Delete snippet action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Delete snippet
        $result = Model::deleteById($this->args['id']);
        if ($result) {
            $this->view->setSuccessAlert(translate('Successfully deleted'));

            return $this->redirectToRoute('tmod_snippets_backend_index');
        }
        throw new RuntimeException('Deleting snippet failed (ID: '.$this->args['id'].')');
    }
}
