<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ThemeModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class ThemeController extends BackendController
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
        $this->view->setTitle(translate('Theme', [], true))->addBreadcrumb(translate('Extension', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/theme/index', [
            'themes' => ThemeModel::findAll(),
        ]);
    }

    /**
     * Delete theme action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        try {
            // Get theme
            $theme = ThemeModel::findById($this->args['id']);

            // Delete theme
            if ($theme && $theme->delete()) {
                $this->service('alert')->success(translate('Successfully deleted'));
            } else {
                throw new RuntimeException('Deleting theme failed (ID: '.$this->args['id'].')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('backend_theme_index');
    }

    /**
     * Install action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function installAction(): RedirectResponse
    {
        $uploadedItem = $this->request()->getFile('package');

        try {
            $file = $this->service('upload')->move($uploadedItem, $this->config()->getTempPath(), true, ['zip']);

            $theme = new ThemeModel();

            if ($theme->install($file) && $theme->validate() && $theme->save()) {
                $this->service('alert')->success(translate('Successfully installed'));
            } else {
                throw new RuntimeException('Installing theme failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Install theme failed'), [$ex->getMessage()]]);
        } catch (Exception $ex) {
            $this->service('alert')->danger([
                translate('Install theme failed, see error message'),
                [$ex->getMessage()],
            ]);
        }

        return $this->redirectToRoute('backend_theme_index');
    }

    /**
     * Update theme action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function updateAction(): RedirectResponse
    {
        $uploadedItem = $this->request()->getFile('file');
        $theme_id = $this->request()->getPost('theme_id');

        try {
            $file = $this->service('upload')->move($uploadedItem, $this->config()->getTempPath(), true, ['zip']);

            $theme = ThemeModel::findById($theme_id);

            if ($theme && $theme->installUpdate($file)) {
                $this->service('alert')->success(translate('Theme successfully updated'));
            } else {
                throw new RuntimeException('Updating theme failed (ID: '.$theme_id.')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([translate('Update theme failed'), [$ex->getMessage()]]);
        } catch (Exception $ex) {
            $this->service('alert')->danger([translate('Update theme failed, see error message'), [$ex->getMessage()]]);
        }

        return $this->redirectToRoute('backend_theme_view', ['id' => $theme_id]);
    }

    /**
     * View theme action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function viewAction(): Response
    {
        // Get theme
        $theme = ThemeModel::findById($this->args['id']);

        if ($theme) {
            // Set title and breadcrumb
            $this->view->setTitle($theme->name)->setSubtitle('ID: '.$theme->id())->addBreadcrumb(translate('Theme', [], true), generate_url('backend_theme_index'));

            // Set back url
            $this->view->setBackRoute('backend_theme_index');

            return $this->render('backend/theme/view', [
                'theme' => $theme,
            ]);
        }

        throw new RuntimeException('Theme not found (ID: '.$this->args['id'].')');
    }

    /**
     * Reload themes action.
     *
     * @return RedirectResponse
     */
    public function reloadAction(): RedirectResponse
    {
        $errors = [];

        // Get themes
        $themes = ThemeModel::findAll();
        if (isset($this->args['id'])) {
            $themes = $themes->where('theme_id', $this->args['id']);
            if (0 === $themes->count()) {
                throw new RuntimeException('Reloading theme failed (ID: '.$this->args['id'].')');
            }
        }
        // Reload all themes
        foreach ($themes as $theme) {
            try {
                $theme->reload();
            } catch (ValidationException $ex) {
                $errors[] = translate('Reload failed for {0}', [$theme->name]);
                $errors[] = $ex->getErrors();
            }
        }

        if (count($errors) > 0) {
            $this->service('alert')->danger($errors);
        } else {
            $this->service('alert')->success(translate('Successfully reloaded'));
        }

        if (isset($this->args['id'])) {
            return $this->redirectToRoute('backend_theme_view', ['id' => $this->args['id']]);
        }

        return $this->redirectToRoute('backend_theme_index');
    }
}
