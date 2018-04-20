<?php

use Neoflow\Framework\App;

/**
 * Translate key and values.
 *
 * @param string $key             Translation key
 * @param array  $values          Values for translation
 * @param bool   $plural          Set TRUE when translation should be plural
 * @param bool   $errorPrefix     Set FALSE to prevent the error prefix get added
 * @param bool   $translateValues List of values for the translation
 *
 * @return string
 */
function translate(string $key, array $values = [], bool $plural = false, bool $errorPrefix = true, bool $translateValues = true): string
{
    return App::instance()->get('translator')->translate($key, $values, $plural, $errorPrefix, $translateValues);
}
