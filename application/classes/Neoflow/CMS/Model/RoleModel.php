<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\EntityValidator;
use Neoflow\Framework\ORM\Repository;
use Neoflow\Validation\ValidationException;
use function translate;

class RoleModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'roles';

    /**
     * @var string
     */
    public static $primaryKey = 'role_id';

    /**
     * @var array
     */
    public static $properties = ['role_id', 'title', 'description'];

    /**
     * Get repository to fetch permissions.
     *
     * @return Repository
     */
    public function permissions(): Repository
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\PermissionModel', '\\Neoflow\\CMS\\Model\\RolePermissionModel', 'role_id', 'permission_id');
    }

    /**
     * Get repository to fetch pages.
     *
     * @return Repository
     */
    public function pages(): Repository
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\PageModel', '\\Neoflow\\CMS\\Model\\PageRoleModel', 'role_id', 'page_id');
    }

    /**
     * Get repository to fetch users.
     *
     * @return Repository
     */
    public function users(): Repository
    {
        return $this->hasMany('\\Neoflow\\CMS\\Model\\UserModel', 'role_id');
    }

    /**
     * Validate setting entity.
     *
     * @return bool
     */
    public function validate(): bool
    {
        $validator = new EntityValidator($this);

        $validator
            ->required()
            ->betweenLength(3, 20)
            ->callback(function ($title, $role) {
                $roles = RoleModel::repo()
                    ->where('title', '=', $title)
                    ->where('role_id', '!=', $role->id())
                    ->fetchAll();

                return 0 === $roles->count();
            }, '{0} has to be unique', [$this])
            ->set('title', 'Title');

        $validator
            ->maxLength(150)
            ->set('description', 'Description');

        return (bool) $validator->validate();
    }

    /**
     * Save role.
     *
     * @param bool $preventCacheClearing Prevent that the cached database results will get deleted
     *
     * @return bool
     */
    public function save(bool $preventCacheClearing = false): bool
    {
        if (1 !== $this->id() && parent::save($preventCacheClearing)) {
            if (is_array($this->permission_ids)) {
                // Delete old role permissions
                RolePermissionModel::deleteAllByColumn('role_id', $this->id());

                // Create new role permissions
                foreach ($this->permission_ids as $permission_id) {
                    RolePermissionModel::create([
                            'role_id' => $this->id(),
                            'permission_id' => $permission_id,
                        ])
                        ->save($preventCacheClearing);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Delete role.
     *
     * @return bool
     *
     * @throws ValidationException
     */
    public function delete(): bool
    {
        if (1 !== $this->id()) {
            if (!$this->users()->count()) {
                // Delete role pages
                PageRoleModel::deleteAllByColumn('role_id', $this->id());

                // Delete role permissions
                RolePermissionModel::deleteAllByColumn('role_id', $this->id());

                return parent::delete();
            } else {
                throw new ValidationException(translate('Role is in use and cannot be deleted'));
            }
        }

        return false;
    }

    /**
     * Getter.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        $value = parent::__get($name);

        if (!$value && 'permission_ids' === $name) {
            $value = $this->permissions()->fetchAll()->mapValue('permission_id');

            $this->set('permission_ids', $value);
        }

        return $value;
    }
}
