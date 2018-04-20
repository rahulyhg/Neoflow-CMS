<?php

use Neoflow\Framework\App;

/**
 * Generate URL of route.
 *
 * @param string $key Route key
 * @param array  $args URL path arguments
 * @param array  $parameters URL query parameters
 * @param string $languageCode URL language code
 *
 * @return string
 */
function generate_url(string $key, array $args = [], array $parameters = [], string $languageCode = ''): string
{
    return App::instance()->get('router')->generateUrl($key, $args, $parameters, $languageCode);
}
