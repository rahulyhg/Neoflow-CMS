<?php

use Neoflow\Framework\App;

/**
 * Translate key and values.
 *
 * @param string $key             Translation key
 * @param array  $values          Values for translation
 * @param bool   $plural          State if translation should be plural
 * @param bool   $errorPrefix     State if prefix should be added when an error appears
 * @param bool   $translateValues State whether values should be translated too
 *
 * @return string
 */
function translate($key, array $values = [], $plural = false, $errorPrefix = true, $translateValues = true)
{
    return App::instance()->get('translator')->translate($key, $values, $plural, $errorPrefix, $translateValues);
}
