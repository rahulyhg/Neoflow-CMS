<?php

/**
 * Normalize class name.
 *
 * @param string $className
 *
 * @return string
 */
function normalize_class_name($className)
{
    $normalized = str_replace('\\', '/', $className);
    $trimmed = ltrim($normalized, '\\');

    return $trimmed;
}
