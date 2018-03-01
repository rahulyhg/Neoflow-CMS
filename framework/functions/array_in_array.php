<?php

/**
 * Check whether array is in array.
 *
 * @param array $needle
 * @param array $haystack
 *
 * @return bool
 */
function array_in_array(array $needle, array $haystack)
{
    $difference = array_diff($needle, $haystack);

    return 0 === count($difference);
}
