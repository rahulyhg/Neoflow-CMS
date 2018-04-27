<?php

use Neoflow\CMS\App;

/**
 * Check whether route is current.
 *
 * @param mixed $keys              Route keys
 * @param mixed $returnValue       Return value
 * @param mixed $returnFailedValue Return value when not current
 *
 * @return mixed
 */
function is_current_route($keys, $returnValue = true, $returnFailedValue = false)
{
    if (App::instance()->get('router')->isCurrentRoute($keys)) {
        return $returnValue;
    }

    return $returnFailedValue;
}
