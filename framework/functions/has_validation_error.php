<?php

use Neoflow\Framework\App;

/**
 * Check whether validation error exists.
 *
 * @param string $key
 * @param mixed  $returnValue
 *
 * @return mixed
 */
function has_validation_error($key = '', $returnValue = true)
{
    if (App::instance()->getService('validation')->hasError($key)) {
        return $returnValue;
    }

    return false;
}
