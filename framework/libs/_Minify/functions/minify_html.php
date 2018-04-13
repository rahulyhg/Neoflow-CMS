<?php

/**
 * Minify HTML source.
 *
 * @param string $html
 *
 * @return string
 */
function minify_html(string $html): string
{
    $search = [
        '/\>[^\S ]+/s', // strip whitespaces after tags, except space
        '/[^\S ]+\</s', // strip whitespaces before tags, except space
        '/(\s)+/s', // shorten multiple whitespace sequences
        '/\>[\s]+\</s',
    ];

    $replace = [
        '>',
        '<',
        '\\1',
        '><',
    ];

    return preg_replace($search, $replace, $html);
}
