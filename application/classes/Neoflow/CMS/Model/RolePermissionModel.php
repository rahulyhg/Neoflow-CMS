<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class RolePermissionModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'roles_permissions';

    /**
     * @var string
     */
    public static $primaryKey = 'role_permission_id';

    /**
     * @var array
     */
    public static $properties = ['role_permission_id', 'role_id', 'permission_id'];

    /**
     * Get repository to fetch permission.
     *
     * @return Repository
     */
    public function permission(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\PermissionModel', 'permission_id');
    }

    /**
     * Get repository to fetch role.
     *
     * @return Repository
     */
    public function role(): Repository
    {
        return $this->belongsTo('Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }
}
