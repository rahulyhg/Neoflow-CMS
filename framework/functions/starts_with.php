<?php

/**
 * Check whether string starts with another string.
 *
 * @see https://stackoverflow.com/a/834355
 *
 * @param string $haystack The string to search in
 * @param string $needle   Search string
 *
 * @return string
 */
function starts_with(string $haystack, string $needle)
{
    $length = strlen($needle);

    return substr($haystack, 0, $length) === $needle;
}
