<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class PermissionModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'permissions';

    /**
     * @var string
     */
    public static $primaryKey = 'permission_id';

    /**
     * @var array
     */
    public static $properties = ['permission_id', 'title', 'description', 'permission_key'];

    /**
     * Get repository to fetch roles.
     *
     * @return Repository
     */
    public function roles(): Repository
    {
        return $this->hasManyThrough('\\Neoflow\\CMS\\Model\\RoleModel', '\\Neoflow\\CMS\\Model\\RolePermissionModel', 'permission_id', 'role_id');
    }
}
