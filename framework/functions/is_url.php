<?php

/**
 * Check whether string is valid url.
 *
 * @param string $string
 *
 * @return bool
 */
function is_url($string)
{
    return (bool) filter_var($string, FILTER_VALIDATE_URL);
}
