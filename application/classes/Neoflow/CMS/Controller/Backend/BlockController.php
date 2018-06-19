<?php

namespace Neoflow\CMS\Controller\Backend;

use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\BlockModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class BlockController extends BackendController
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

        // Set title and breadcrumb
        $this->view->setTitle(translate('Block', [], true))->addBreadcrumb(translate('Content'));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $this->service('alert')->success(translate('Successfully created'), 'now');

        return $this->render('backend/block/index', ['blocks' => BlockModel::findAll()]);
    }

    /**
     * Create block action.
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

            // Create block
            $block = BlockModel::create(['title' => $postData->get('title'), 'block_key' => $postData->get('block_key')]);

            // Validate and save block
            if ($block && $block->validate() && $block->save()) {
                $this->service('alert')->success(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating block failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Create failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_block_index');
    }

    /**
     * Load blocks action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function loadAction(): RedirectResponse
    {
        try {
            // Get frontend theme
            $frontendTheme = $this->settings()->getFrontendTheme();

            // Validate and save block
            if ($frontendTheme && $frontendTheme->loadBlocks()) {
                $this->service('alert')->success(translate('Successfully created'));
            } else {
                throw new RuntimeException('Creating block failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('backend_block_index');
    }

    /**
     * Edit block action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function editAction(): Response
    {
        // Get block or data if validation has failed
        $block = BlockModel::findById($this->args['id']);
        if ($this->service('validation')->hasError()) {
            $data = $this->service('validation')->getData();
            $block = BlockModel::updateById($data, $data['block_id']);
        }

        if ($block) {
            // Set title and breadcrumb
            $this->view->setTitle($block->title)->setSubtitle('ID: '.$block->id())->addBreadcrumb(translate('Block', [], true), generate_url('backend_block_index'));

            // Set back url
            $this->view->setBackRoute('backend_block_index');

            return $this->render('backend/block/edit', ['block' => $block]);
        }
        throw new RuntimeException('Block not found (ID: '.$this->args['id'].')');
    }

    /**
     * Update user action.
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

            // Update block
            $block = BlockModel::updateById(['title' => $postData->get('title'), 'block_key' => $postData->get('block_key')], $postData->get('block_id'));

            // Validate and save block
            if ($block && $block->validate() && $block->save()) {
                $this->service('alert')->success(translate('Successfully updated'));
            } else {
                throw new RuntimeException('Updating block failed (ID: '.$postData->get('block_id').')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update failed'), $ex->getErrors()]);
        }

        return $this->redirectToRoute('backend_block_edit', ['id' => $block->id()]);
    }

    /**
     * Delete block action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Get and delete block
        $block = BlockModel::findById($this->args['id']);
        if ($block && $block->delete()) {
            $this->service('alert')->success(translate('Successfully deleted'));

            return $this->redirectToRoute('backend_block_index');
        }
        throw new RuntimeException('Deleting block failed (ID: '.$this->args['id'].')');
    }

    /**
     * Check permission.
     *
     * @return bool
     */
    protected function checkPermission(): bool
    {
        return has_permission('manage_blocks');
    }
}
