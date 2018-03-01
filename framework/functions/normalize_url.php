<?php

/**
 * Normalize URL.
 *
 * @param string $url
 *
 * @return string
 */
function normalize_url($url)
{
    $normalized = preg_replace('/([^:])(\/{2,})/', '$1/', $url);
    $slashed = str_replace('\\', '/', $normalized);
    $trimmed = rtrim($slashed, '/');

    return $trimmed;
}
