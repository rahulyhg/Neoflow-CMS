<?php

use Neoflow\Framework\App;

/**
 * Generate URL of route.
 *
 * @param string $routeKey
 * @param array  $args
 * @param array  $params
 * @param string $languageCode
 *
 * @return string
 */
function generate_url($routeKey, $args = [], $params = [], $languageCode = '')
{
    return App::instance()->get('router')->generateUrl($routeKey, $args, $params, $languageCode);
}
