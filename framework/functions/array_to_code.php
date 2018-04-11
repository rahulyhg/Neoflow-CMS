<?php

/**
 * Create PHP code of array
 * @param array $array Array
 * @param string $space Formatting space (e.g. "\t" or "\s")
 * @param string $endOfArray End of array separator
 * @return string
 */
function array_to_code(array $array, string $space = '', string $endOfArray = ';')
{
    $content = '[' . PHP_EOL;

    foreach ($array as $key => $value) {
        $content .= "\t" . $space;
        if (is_string($key)) {
            $content .= '\'' . $key . '\'' . ' => ';
        }
        if (is_array($value)) {
            $content .= array_to_code($value, $space . "\t", ',');
        } else if (is_bool($value)) {
            $content .= ($value ? 'true' : 'false') . ',' . PHP_EOL;
        } else if (is_string($value)) {
            $content .= '\'' . $value . '\'' . ',' . PHP_EOL;
        } else {
            $content .= $value . ',' . PHP_EOL;
        }
    }

    $content .= $space . ']' . $endOfArray . PHP_EOL;

    return $content;
}
