<?php

use Neoflow\CMS\App;

/**
 * Check whether route is active.
 *
 * @param array|string $routeKeys
 * @param mixed        $returnValue
 * @param mixed        $returnFailedValue
 *
 * @return mixed
 */
function is_current_route($routeKeys, $returnValue = true, $returnFailedValue = false)
{
    if (App::instance()->get('router')->isCurrentRoute($routeKeys)) {
        return $returnValue;
    }

    return $returnFailedValue;
}
