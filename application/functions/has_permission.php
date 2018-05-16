<?php

use Neoflow\CMS\App;

/**
 * Check whether authenticated user has permission.
 *
 * @param string|array $permissionKeys
 *
 * @return bool
 */
function has_permission($permissionKeys): bool
{
    return App::instance()->service('auth')->hasPermission($permissionKeys);
}
