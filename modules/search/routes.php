<?php
// Module routes
return [
    'routes' => [
        ['tmod_search_backend_index', 'get', '/backend/module/search', 'Backend@index'],
        ['tmod_search_frontend_index', 'get', '/search', 'Frontend@index'],
    ],
    'namespace' => '\\Neoflow\\Module\\Search\\Controller\\',
];
