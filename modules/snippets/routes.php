<?php

// Module routes
return [
    'routes' => [
        ['tmod_snippets_backend_index', 'get', '/backend/module/snippets', 'Backend@index'],
        ['tmod_snippets_backend_edit', 'get', '/backend/module/snippet/edit/(id:num)', 'Backend@edit'],
        ['tmod_snippets_backend_delete', 'get', '/backend/module/snippet/delete/(id:num)', 'Backend@delete'],
        ['tmod_snippets_backend_create', 'post', '/backend/module/snippet/create', 'Backend@create'],
        ['tmod_snippets_backend_update', 'post', '/backend/module/snippet/update', 'Backend@update'],
    ],
    'namespace' => 'Neoflow\\Module\\Snippets\\Controller\\',
];
