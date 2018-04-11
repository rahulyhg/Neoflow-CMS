<?php

/**
 * Exports an array to a parsable string.
 *
 * @param array  $array       Array to export
 * @param string $indentation Indentation space (e.g. "   " or "\s")
 * @param string $endOfArray  End of array separator
 *
 * @return string
 */
function array_export(array $array, string $indentation = '    ', string $endOfArray = ';', int $level = 1)
{
    $content = '[' . PHP_EOL;

    foreach ($array as $key => $value) {
        $content .= $indentation;
        if (is_string($key)) {
            $content .= '\'' . $key . '\'' . ' => ';
        }
        if (is_array($value)) {
            $content .= array_export($value, str_repeat($indentation, $level + 1), ',', $level + 1);
        } elseif (is_bool($value)) {
            $content .= ($value ? 'true' : 'false') . ',' . PHP_EOL;
        } elseif (is_string($value)) {
            $content .= '\'' . $value . '\'' . ',' . PHP_EOL;
        } else {
            $content .= $value . ',' . PHP_EOL;
        }
    }

    if ($level > 0) {
        $content .= $indentation;
    }

    $content .= ']' . $endOfArray . PHP_EOL;

    return $content;
}
