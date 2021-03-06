<?php

namespace Neoflow\CMS\Controller\Backend;

use Exception;
use Neoflow\CMS\Controller\BackendController;
use Neoflow\CMS\Model\ModuleModel;
use Neoflow\CMS\View\BackendView;
use Neoflow\Framework\HTTP\Responsing\RedirectResponse;
use Neoflow\Framework\HTTP\Responsing\Response;
use Neoflow\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ModuleController extends BackendController
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
        $this->view->setTitle(translate('Module', [], true))->addBreadcrumb(translate('Extension', [], true));
    }

    /**
     * Index action.
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('backend/module/index', [
                'modules' => ModuleModel::findAll(),
            ]);
    }

    /**
     * Toggle page activation action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function toggleActivationAction(): RedirectResponse
    {
        // Get module and toggle activation
        $module = ModuleModel::findById($this->args['id']);

        try {
            if ($module && $module->toggleActivation() && $module->save()) {
                if ($module->is_active) {
                    $this->service('alert')->success(translate('Successfully enabled'));
                } else {
                    $this->service('alert')->success(translate('Successfully disabled'));
                }
            } else {
                throw new RuntimeException('Toggling activation for module failed (ID: '.$this->args['id'].')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('backend_module_index');
    }

    /**
     * Delete module action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function deleteAction(): RedirectResponse
    {
        // Get module
        $module = ModuleModel::findById($this->args['id']);

        try {
            // Delete module
            if ($module && $module->delete()) {
                $this->service('alert')->success(translate('Successfully deleted'));
            } else {
                throw new RuntimeException('Deleting module failed (ID: '.$this->args['id'].')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->warning($ex->getErrors());
        }

        return $this->redirectToRoute('backend_module_index');
    }

    /**
     * Install action.
     *
     * @return RedirectResponse
     */
    public function installAction(): RedirectResponse
    {
        $uploadedItem = $this->request()->getFile('package');

        try {
            $file = $this->service('upload')->move($uploadedItem, $this->config()->getTempPath(), true, ['zip']);

            if (ModuleModel::installPackage($file)) {
                $this->service('alert')->success(translate('Successfully installed'));
            } else {
                throw new RuntimeException('Installing module failed');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([
                    translate('Install module failed'),
                    [$ex->getMessage()],
                ]);
        } catch (Exception $ex) {
            $this->service('alert')->danger([
                    translate('Install module failed, see error message'),
                    [$ex->getMessage()],
                ]);
        }

        return $this->redirectToRoute('backend_module_index');
    }

    /**
     * Update theme action.
     *
     * @return RedirectResponse
     */
    public function updateAction(): RedirectResponse
    {
        $uploadedItem = $this->request()->getFile('file');
        $module_id = $this->request()->getPost('module_id');

        try {
            $file = $this->service('upload')->move($uploadedItem, $this->config()->getTempPath(), true, ['zip']);

            $module = ModuleModel::findById($module_id);

            if ($module && $module->installUpdate($file)) {
                $this->service('alert')->success(translate('Module successfully updated'));
            } else {
                throw new RuntimeException('Updating module failed (ID: '.$module_id.')');
            }
        } catch (ValidationException $ex) {
            $this->service('alert')->danger([
                    translate('Update module failed'),
                    [$ex->getMessage()],
                ]);
        } catch (Throwable $ex) {
            $this->service('alert')->danger([
                    translate('Update module failed, see error message'),
                    [$ex->getMessage()],
                ]);
        }

        return $this->redirectToRoute('backend_module_view', ['id' => $module_id]);
    }

    /**
     * View module action.
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function viewAction(): Response
    {
        // Get module
        $module = ModuleModel::findById($this->args['id']);

        if ($module) {
            // Set title and breadcrumb
            $this->view->setTitle($module->name)->setSubtitle('ID: '.$module->id())->addBreadcrumb(translate('Module', [], true), generate_url('backend_module_index'));

            // Set back url
            $this->view->setBackRoute('backend_module_index');

            $requiredModules = [];
            $requiredModuleIdentifiers = $module->getRequiredModuleIdentifiers();
            foreach ($requiredModuleIdentifiers as $requiredModuleIdentifier) {
                $requiredModules[$requiredModuleIdentifier] = ModuleModel::findByColumn('identifier', $requiredModuleIdentifier);
            }

            return $this->render('backend/module/view', [
                    'module' => $module,
                    'requiredModules' => $requiredModules,
                ]);
        }

        throw new RuntimeException('Module not found (ID: '.$this->args['id'].')');
    }

    /**
     * Reload modules action.
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException
     */
    public function reloadAction(): RedirectResponse
    {
        $errors = [];

        // Get modules
        $modules = ModuleModel::findAll();
        if (isset($this->args['id'])) {
            $modules = $modules->where('module_id', $this->args['id']);
            if (0 === $modules->count()) {
                throw new RuntimeException('Reloading module failed (ID: '.$this->args['id'].')');
            }
        }

        // Reload all themes
        foreach ($modules as $module) {
            try {
                $module->reload();
            } catch (ValidationException $ex) {
                $errors[] = translate('Reload failed for {0}', [$module->name]);
                $errors[] = $ex->getErrors();
            }
        }

        if (count($errors) > 0) {
            $this->service('alert')->danger($errors);
        } else {
            $this->service('alert')->success(translate('Successfully reloaded'));
        }

        if (isset($this->args['id'])) {
            return $this->redirectToRoute('backend_module_view', ['id' => $this->args['id']]);
        }

        return $this->redirectToRoute('backend_module_index');
    }
}
