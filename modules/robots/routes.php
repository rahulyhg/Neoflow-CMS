<?php

// Module routes
return [
    'routes' => [
        ['tmod_robots_backend_index', 'get', '/backend/module/robots', 'Backend@index'],
        ['tmod_robots_backend_update', 'post', '/backend/module/robots/update', 'Backend@update'],
    ],
    'namespace' => '\\Neoflow\\Module\\Robots\\Controller\\',
];
