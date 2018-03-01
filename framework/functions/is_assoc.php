<?php

/**
 * Check whether array has associative data.
 *
 * @param array $array
 *
 * @return bool
 */
function is_assoc($array)
{
    if (is_array($array)) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    return false;
}
