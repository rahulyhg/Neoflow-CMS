<?php

use Neoflow\Framework\App;

/**
 * Check whether validation error exists.
 *
 * @param string $key Optional validation key
 * @param mixed  $returnValue Return value
 * @param mixed        $returnFailedValue Return value when no validation errors
 *
 * @return mixed
 */
function has_validation_error(string $key = '', $returnValue = true, $returnFailedValue = false)
{
    if (App::instance()->getService('validation')->hasError($key)) {
        return $returnValue;
    }

    return $returnFailedValue;
}
