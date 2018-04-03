<?php
namespace Neoflow\CMS\Model;

use InvalidArgumentException;
use Neoflow\CMS\Manager\AbstractModuleManager;
use Neoflow\Framework\ORM\EntityCollection;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Filesystem\File;
use Neoflow\Validation\ValidationException;
use RuntimeException;

class ModuleModel extends AbstractExtensionModel
{

    /**
     * @var string
     */
    public static $tableName = 'modules';

    /**
     * @var string
     */
    public static $primaryKey = 'module_id';

    /**
     * @var array
     */
    public static $properties = [
        'module_id', 'name', 'folder_name', 'frontend_route',
        'backend_route', 'manager_class', 'version', 'description', 'author',
        'type', 'copyright', 'license', 'is_active', 'identifier',
        'dependencies', 'is_core'
    ];

    /**
     * @var array
     */
    public static $types = ['page', 'library', 'tool'];

    /**
     * @var AbstractModuleManager
     */
    protected $manager;

    /**
     * Get repository to fetch sections.
     *
     * @return Repository
     */
    public function sections()
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\SectionModel', 'module_id');
    }

    /**
     * Find all modules by type.
     *
     * @param string $type Module type
     *
     * @return EntityCollection
     *
     * @throws InvalidArgumentException
     */
    public static function findAllByType(string $type)
    {
        if (in_array($type, self::$types)) {
            return parent::findAllByColumn('type', $type);
        }
        throw new InvalidArgumentException('Module type is not valid');
    }

    /**
     * Validate module.
     *
     * @return bool
     */
    public function validate(): bool
    {
        parent::validate();

        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($name, $id) {
                return 0 === ModuleModel::repo()
                    ->where('name', '=', $name)
                    ->where('module_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', array($this->id()))
            ->set('name', 'Name');

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->callback(function ($folder, $id) {
                return 0 === ModuleModel::repo()
                    ->where('folder_name', '=', $folder)
                    ->where('module_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', array($this->id()))
            ->set('folder_name', 'Folder');

        if ('page' === $this->type) {
            $validator
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->set('frontend_route', 'Frontend Routekey');

            $validator
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->set('backend_route', 'Backend Routekey');
        }

        $validator
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->set('identifier', 'Identifier');

        $validator
            ->maxLength(100)
            ->callback(function ($namespace, $id) {
                return 0 === ModuleModel::repo()
                    ->where('manager_class', '=', $namespace)
                    ->where('module_id', '!=', $id)
                    ->count();
            }, '{0} has to be unique', array($this->id()))
            ->set('manager_class', 'Manager Class');

        $validator
            ->oneOf(static::$types)
            ->set('type', 'Type');

        return (bool) $validator->validate();
    }

    /**
     * Toggle activation.
     *
     * @throws ValidationException
     *
     * @return self
     */
    public function toggleActivation()
    {
        if ($this->is_active) {
            if (!$this->hasDependentModules()) {
                $this->is_active = false;
            } else {
                throw new ValidationException(translate('{0} has at least one or more depending modules ({1}) and cannot be disabled', [$this->name, $this->getDependentModules()->mapValue('name')]));
            }
        } else {
            $this->is_active = true;
        }

        return $this;
    }

    /**
     * Get module manager.
     *
     * @return AbstractModuleManager
     *
     * @throws RuntimeException
     */
    public function getManager(): AbstractModuleManager
    {
        if (!$this->manager && class_exists($this->manager_class)) {
            $this->manager = new $this->manager_class($this);
        } elseif (!class_exists($this->manager_class)) {
            throw new RuntimeException('Manager class ' . $this->manager_class . '  not found');
        }

        return $this->manager;
    }

    /**
     * Install module.
     *
     * @return bool
     */
    public function install(File $extensionPackageFile): bool
    {
        if (parent::install($extensionPackageFile)) {
            return $this->getManager()->install();
        }

        return false;
    }

    /**
     * Install and update module.
     *
     * @return bool
     */
    public function installUpdate(File $extensionPackageFile): bool
    {
        if (parent::installUpdate($extensionPackageFile)) {
            return $this->getManager()->update();
        }

        return false;
    }

    /**
     * Check whether the module is dependent to other modules.
     *
     * @return bool
     */
    public function hasDependentModules(): bool
    {
        return self::repo()->where('dependencies', 'LIKE', '%' . $this->identifier . '%')->count() > 0;
    }

    /**
     * Get dependent modules of the module.
     *
     * @return EntityCollection
     */
    public function getDependentModules(): EntityCollection
    {
        return self::repo()->where('dependencies', 'LIKE', '%' . $this->identifier . '%')->fetchAll();
    }

    /**
     * Check whether the module has required modules.
     *
     * @return bool
     */
    public function hasRequiredModules(): bool
    {
        return (bool) $this->dependencies;
    }

    /**
     * Get required module status.
     *
     * @return bool
     */
    public function getRequiredModuleStatus(): bool
    {
        $requiredModuleIdentifiers = $this->getRequiredModuleIdentifiers();
        $requiredModules = $this->getRequiredModules();

        return count($requiredModuleIdentifiers) === $requiredModules->count();
    }

    /**
     * Get identifiers of required modules.
     *
     * @return array
     */
    public function getRequiredModuleIdentifiers(): array
    {
        if ($this->dependencies) {
            return explode(',', $this->dependencies);
        }

        return [];
    }

    /**
     * Get required modules.
     *
     * @return EntityCollection
     */
    public function getRequiredModules(): EntityCollection
    {
        $identifiers = $this->getRequiredModuleIdentifiers();

        return self::repo()->where('identifier', '=', $identifiers)->fetchAll();
    }

    /**
     * Delete module.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function delete()
    {
        if ($this->is_core == false) {
            if (0 === $this->sections()->count()) {
                if (!$this->hasDependentModules()) {
                    if ($this->getManager()->uninstall()) {
                        return parent::delete();
                    }

                    return false;
                }
                throw new ValidationException(translate('{0} has at least one or more depending modules ({1}) and cannot be deleted', [$this->name, $this->getDependentModules()->mapValue('name')]));
            }
            throw new ValidationException(translate('The module ({0}) is in use and cannot be deleted', [$this->name]));
        }
        throw new ValidationException(translate('{0} is a core module and cannot be deleted', [$this->name]));
    }

    /**
     * Get module url.
     *
     * @param string $uri
     *
     * @return string
     */
    public function getUrl($uri = '')
    {
        return $this
                ->config()
                ->getModulesUrl('/' . $this->folder_name . '/' . $uri);
    }

    /**
     * Get module path.
     *
     * @param string $additionalPath
     *
     * @return string
     */
    public function getPath($additionalPath = '')
    {
        return $this
                ->config()
                ->getModulesPath('/' . $this->folder_name . '/' . $additionalPath);
    }
}
