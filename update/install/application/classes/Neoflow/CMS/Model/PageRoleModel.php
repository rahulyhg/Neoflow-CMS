<?php

namespace Neoflow\CMS\Model;

use Neoflow\CMS\Core\AbstractModel;
use Neoflow\Framework\ORM\Repository;

class PageRoleModel extends AbstractModel
{
    /**
     * @var string
     */
    public static $tableName = 'pages_roles';

    /**
     * @var string
     */
    public static $primaryKey = 'page_role_id';

    /**
     * @var array
     */
    public static $properties = ['page_role_id', 'page_id', 'role_id'];

    /**
     * Get repository to fetch role.
     *
     * @return Repository
     */
    public function role(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\RoleModel', 'role_id');
    }

    /**
     * Get repository to fetch page.
     *
     * @return Repository
     */
    public function page(): Repository
    {
        return $this->belongsTo('\\Neoflow\\CMS\\Model\\PageModel', 'page_id');
    }
}
