<?php

// Module routes
return [
    'routes' => [
        ['tmod_sitemap_backend_index', 'get', '/backend/module/sitemap', 'Backend@index'],
        ['tmod_sitemap_backend_recreate', 'get', '/backend/module/sitemap/recreate', 'Backend@recreateSitemap'],
        ['tmod_sitemap_backend_delete', 'get', '/backend/module/sitemap/delete', 'Backend@deleteSitemap'],
        ['tmod_sitemap_backend_update_settings', 'post', '/backend/module/sitemap/settings/update', 'Backend@updateSettings'],
    ],
    'namespace' => '\\Neoflow\\Module\\Sitemap\\Controller\\',
];
