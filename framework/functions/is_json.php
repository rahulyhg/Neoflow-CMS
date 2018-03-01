<?php

/**
 * Check whether string is valid JSON encoded data.
 *
 * @param string $string
 *
 * @return bool
 */
function is_json($string)
{
    json_decode($string);

    return JSON_ERROR_NONE === json_last_error();
}
