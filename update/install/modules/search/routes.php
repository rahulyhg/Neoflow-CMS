<?php

// Module routes
return [
    'routes' => [
        ['tmod_search_backend_index', 'get', '/backend/module/search', 'Backend@index'],
        ['tmod_search_backend_update', 'post', '/backend/module/search/update', 'Backend@update'],
    ],
    'namespace' => '\\Neoflow\\Module\\Search\\Controller\\',
];
